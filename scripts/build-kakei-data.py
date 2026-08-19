"""総務省「家計調査（家計収支編）」から、都市別・費目別の平均支出を取り出す。

出典: 総務省統計局「家計調査 家計収支編 二人以上の世帯 詳細結果表
      第1-1表 都市階級・地方・都道府県庁所在市別1世帯当たり1か月間の収入と支出」
      https://www.e-stat.go.jp/stat-search/files?tclass=000000330002

平均値をそのまま載せる。推計や独自の加工はしない。
"""
import json
import re
import sys
import urllib.request
import xml.etree.ElementTree as ET
import zipfile
from datetime import date
from io import BytesIO
from pathlib import Path

NS = '{http://schemas.openxmlformats.org/spreadsheetml/2006/main}'
STAT_INF_ID = '000040247064'   # 2024年（2025-02-07公開）
DOWNLOAD = 'https://www.e-stat.go.jp/stat-search/file-download?statInfId={}&fileKind=0'
PAGE = 'https://www.e-stat.go.jp/stat-search/files?tclass=000000330002'
UA = ('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 '
      '(KHTML, like Gecko) Chrome/131.0 Safari/537.36')

# 載せる費目。家計調査の「用途分類」の名前をそのまま使う。
ITEMS = [
    ('消費支出', 'total'),
    ('食料', 'food'),
    ('住居', 'housing'),
    ('光熱・水道', 'utilities'),
    ('家具・家事用品', 'furniture'),
    ('被服及び履物', 'clothing'),
    ('保健医療', 'health'),
    ('交通・通信', 'transport'),
    ('教育', 'education'),
    ('教養娯楽', 'culture'),
    ('その他の消費支出', 'others'),
]

CITY_SLUGS = {
    '札幌市': 'sapporo', '青森市': 'aomori', '盛岡市': 'morioka', '仙台市': 'sendai',
    '秋田市': 'akita', '山形市': 'yamagata', '福島市': 'fukushima', '水戸市': 'mito',
    '宇都宮市': 'utsunomiya', '前橋市': 'maebashi', 'さいたま市': 'saitama', '千葉市': 'chiba',
    '東京都区部': 'tokyo', '横浜市': 'yokohama', '新潟市': 'niigata', '富山市': 'toyama',
    '金沢市': 'kanazawa', '福井市': 'fukui', '甲府市': 'kofu', '長野市': 'nagano',
    '岐阜市': 'gifu', '静岡市': 'shizuoka', '名古屋市': 'nagoya', '津市': 'tsu',
    '大津市': 'otsu', '京都市': 'kyoto', '大阪市': 'osaka', '神戸市': 'kobe',
    '奈良市': 'nara', '和歌山市': 'wakayama', '鳥取市': 'tottori', '松江市': 'matsue',
    '岡山市': 'okayama', '広島市': 'hiroshima', '山口市': 'yamaguchi', '徳島市': 'tokushima',
    '高松市': 'takamatsu', '松山市': 'matsuyama', '高知市': 'kochi', '福岡市': 'fukuoka',
    '佐賀市': 'saga', '長崎市': 'nagasaki', '熊本市': 'kumamoto', '大分市': 'oita',
    '宮崎市': 'miyazaki', '鹿児島市': 'kagoshima', '那覇市': 'naha', '川崎市': 'kawasaki',
    '相模原市': 'sagamihara', '浜松市': 'hamamatsu', '堺市': 'sakai', '北九州市': 'kitakyushu',
}

CITY_PREFECTURES = {
    '札幌市': '北海道', '青森市': '青森県', '盛岡市': '岩手県', '仙台市': '宮城県',
    '秋田市': '秋田県', '山形市': '山形県', '福島市': '福島県', '水戸市': '茨城県',
    '宇都宮市': '栃木県', '前橋市': '群馬県', 'さいたま市': '埼玉県', '千葉市': '千葉県',
    '東京都区部': '東京都', '横浜市': '神奈川県', '新潟市': '新潟県', '富山市': '富山県',
    '金沢市': '石川県', '福井市': '福井県', '甲府市': '山梨県', '長野市': '長野県',
    '岐阜市': '岐阜県', '静岡市': '静岡県', '名古屋市': '愛知県', '津市': '三重県',
    '大津市': '滋賀県', '京都市': '京都府', '大阪市': '大阪府', '神戸市': '兵庫県',
    '奈良市': '奈良県', '和歌山市': '和歌山県', '鳥取市': '鳥取県', '松江市': '島根県',
    '岡山市': '岡山県', '広島市': '広島県', '山口市': '山口県', '徳島市': '徳島県',
    '高松市': '香川県', '松山市': '愛媛県', '高知市': '高知県', '福岡市': '福岡県',
    '佐賀市': '佐賀県', '長崎市': '長崎県', '熊本市': '熊本県', '大分市': '大分県',
    '宮崎市': '宮崎県', '鹿児島市': '鹿児島県', '那覇市': '沖縄県', '川崎市': '神奈川県',
    '相模原市': '神奈川県', '浜松市': '静岡県', '堺市': '大阪府', '北九州市': '福岡県',
}


def workbook() -> zipfile.ZipFile:
    cache = Path(__file__).resolve().parent / f'kakei-{STAT_INF_ID}.xlsx'

    if not cache.exists():
        request = urllib.request.Request(DOWNLOAD.format(STAT_INF_ID), headers={'User-Agent': UA})
        with urllib.request.urlopen(request, timeout=180) as response:
            cache.write_bytes(response.read())

    return zipfile.ZipFile(cache)


def sheet_rows(archive: zipfile.ZipFile) -> list[dict]:
    shared = []

    for si in ET.fromstring(archive.read('xl/sharedStrings.xml')).findall(NS + 'si'):
        parts = []
        for child in si:
            if child.tag == NS + 't':
                parts.append(child.text or '')
            elif child.tag == NS + 'r':
                parts.extend(t.text or '' for t in child.findall(NS + 't'))
        shared.append(''.join(parts))

    rows = []
    for row in ET.fromstring(archive.read('xl/worksheets/sheet1.xml')).iter(NS + 'row'):
        cells = {}
        for cell in row.findall(NS + 'c'):
            column = re.match(r'[A-Z]+', cell.get('r')).group(0)
            value = cell.find(NS + 'v')
            if value is None:
                continue
            cells[column] = shared[int(value.text)] if cell.get('t') == 's' else value.text
        rows.append(cells)

    return rows


def to_int(value: str | None) -> int | None:
    if value is None or value in ('...', '-', ''):
        return None
    try:
        return int(round(float(value)))
    except ValueError:
        return None


def main() -> None:
    output = Path(sys.argv[1])
    rows = sheet_rows(workbook())
    header = rows[8]

    # 列見出し「01100 札幌市」から都市名を取る
    city_columns = {}
    for column, label in header.items():
        match = re.match(r'^\d{5}\s+(.+)$', str(label))
        if match:
            city_columns[column] = match.group(1)

    wanted = dict(ITEMS)
    values = {}      # 費目 -> {列 -> 金額}
    households = {}  # 世帯人員

    for row in rows[9:]:
        name = (row.get('L') or '').strip()

        if name in wanted and name not in values:
            values[name] = row
        elif name == '世帯人員' and not households:
            households = row

    if len(values) < len(wanted):
        raise RuntimeError('費目を読み取れませんでした: ' + str(set(wanted) - set(values)))

    cities = []
    for column, city in city_columns.items():
        spending = {}
        for name, key in ITEMS:
            spending[key] = to_int(values[name].get(column))

        if spending['total'] is None:
            continue

        cities.append({
            'name': city,
            'slug': CITY_SLUGS.get(city),
            'prefecture': CITY_PREFECTURES.get(city),
            'householdSize': float(households.get(column)) if households.get(column) not in (None, '...') else None,
            'spending': spending,
        })

    national = {key: to_int(values[name].get('N')) for name, key in ITEMS}

    output.parent.mkdir(parents=True, exist_ok=True)
    output.write_text(json.dumps({
        'confirmedOn': date.today().isoformat(),
        'surveyYear': 2024,
        'household': '二人以上の世帯',
        'sourceLabel': '総務省統計局「家計調査 家計収支編 二人以上の世帯 詳細結果表 '
                       '第1-1表 都市階級・地方・都道府県庁所在市別1世帯当たり1か月間の収入と支出」（2024年）',
        'sourceUrl': PAGE,
        'items': [{'name': name, 'key': key} for name, key in ITEMS],
        'national': national,
        'nationalHouseholdSize': float(households.get('N')) if households.get('N') else None,
        'cities': sorted(cities, key=lambda c: c['name']),
    }, ensure_ascii=False), encoding='utf-8')

    print(f'{len(cities)}都市を書き出しました（全国の消費支出 {national["total"]:,}円）')


main()
