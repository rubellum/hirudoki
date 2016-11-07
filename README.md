
# 初期設定

```
$ composer install
```

## 設定ファイル 

`.env.example`をコピーして、`.env`を作成してください。

```
LOG_PATH：ログのファイルパス 例）/path/to/app/storage/logs/log.txt
GNAVI_ACCESS_TOKEN：ぐるなびのアクセストークン
CHANNEL_ID：LINEの「Channel ID」
CHANNEL_ACCESS_TOKEN：LINEの「Channel Access Token」
CHANNEL_SECRET：LINEの「Channel Secret」

MYSQL_HOST：データベースのホスト
MYSQL_DATABASE：データベースの名前
MYSQL_USER：データベースのユーザー名
MYSQL_PASS：データベースのパスワード
```

## DB

DBのテーブルは`data/db/schema.sql`で作成してください。

## ぐるなびのカテゴリデータ.

より詳細な検索のため、ぐるなびの大分類・中分類を使用します。
下記のコマンドをcron等で、毎日実行＆更新しましょう。

```
$ php cli batch_category.php
```
