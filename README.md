# くらしの節約データ（enjoy-setsuyaku.jp）

総務省「家計調査」をもとに、費目別・都市別の平均支出を掲載し、
そこから何をどの順番で見直すかを記事で説明するサイト。

## 中身

| 種類 | 置き場所 | 件数 |
|---|---|---|
| 記事 | `resources/articles/*.md` | 13本 |
| 支出データ | `database/data/kakei-2024.json` | 52都市 × 11費目 |

データベースは使わない。記事は Markdown ファイル、支出データは JSON を読む。

## データの更新

家計調査の年報が新しくなったら、`scripts/build-kakei-data.py` の
`STAT_INF_ID` を新しい年の統計表IDに変えて実行する。

```
python scripts/build-kakei-data.py database/data/kakei-2024.json
```

統計表IDは e-Stat の
「家計調査 家計収支編 二人以上の世帯 詳細結果表 第1-1表 都市階級・地方・
都道府県庁所在市別1世帯当たり1か月間の収入と支出」のページで確認する。

## 記事の書き方

`resources/articles/` に Markdown ファイルを置く。先頭に次を書く。

```
---
title: 記事のタイトル
description: 検索結果に出る説明文
category: カテゴリ名
published_on: 2026-08-19
updated_on: 2026-09-01   （任意）
---
```

守っていること:

- 金額・制度は公的機関の公表資料にもとづき、記事末に出典URLを書く
- 「必ず◯円下がる」といった断定はしない
- 条件で結果が変わるものは、変わる理由を書く

## デプロイ

main へ push すると GitHub Actions が Xserver へ rsync する。
必要なシークレット: `SSH_HOST` `SSH_USERNAME` `SSH_PRIVATE_KEY` `APP_KEY`。
任意: `CONTACT_ADDRESS` `GA_MEASUREMENT_ID` `GOOGLE_SITE_VERIFICATION` `ADSENSE_CLIENT`。
