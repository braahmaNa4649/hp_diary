# 一行日記

短文と画像をアップロード出来る日記アプリ

---

## スクリーンショット
- 一覧画面
![一覧画面](https://github.com/user-attachments/assets/f795e860-5376-4403-9a8b-59f4bb921870)

- 登録画面
![登録画面](https://github.com/user-attachments/assets/6f1b0a01-3914-4738-98ee-bd7a28005076)

---

## 仕様

- 一覧
  - 本文と画像のリスト
  - ユーザ自身の日記しか表示されない
- 登録
  - 本文は半角400文字、全角200文字まで
  - 画像はjpgのみ、サイズは2MBまで
    - オプショナルなので、無指定の場合はnoimage画像が設定される
    - 一旦指定しても右上×ボタンでクリア可能
- 編集
  - 登録に準ずる
  - ユーザ自身の日記しか編集出来ない
- 削除
  - 一覧画面のそれぞれの日記に削除ボタン設置
  - アラート確認の上削除
  - 画像も削除される（別の日記の同一画像は削除されない
  - ユーザ自身の日記しか削除出来ない
- ユーザ認証
  - 登録
    - 名前
    - メールアドレス
    - パスワード
  - ログイン
    - メールアドレス
    - パスワード
  - 削除
- ページネーション
  - 5件
- バリデーション
  - JSとPHP両方で行っている
  - サイズオーバーの画像を一旦サーバに上げる、等の無駄が発生しない

**下記の箇所を変更するだけで、バリデーション、メッセージ等全て変更される**  
(本文長と無指定時画像はDBの構成を読み取っている為
- env等で変更可能な項目
  - 画像サイズ
    - envの`MAX_IMAGE_SISE`をバイトで記述
  - 画像タイプ
    - envの`IMAGE_TYPE`に記述
  - ページネーションする件数
    - envの`PAGINATION_COUNT`に記述
  - 本文の長さ
    - `diaries.content`カラムの長さを変更
  - 無指定時画像のファイル名
    - `diaries.file_name`カラムのデフォルト値を変更

---

## 使用技術 / 動作環境

- 言語: PHP8.2 / JavaScript
- フレームワーク:  Laravel / Vue.js 
- その他: MySQL8.0 / Docker / Nginx

**sail等は使わずスクラッチで組んでいる**

---

## ディレクトリ構成

- hp_diary/
  - LICENSE
  - README.md
  - init.sh（初期設定スクリプト
  - app/ (Laravelソースコード群
    - artisan
    - app/
      - UseCases/（controllerから呼ばれる各ロジック
      - Repositories/（UseCaseから呼ばれるリソースの管理・抽象化
  - docker/
    - docker-compose.yml
    - web/
    - db/
    - app/

---
## インストール方法 / 使い方

1. `hp_diary/init.sh`をdockerと同じ権限で実行
1. `sudo`で実行しているコマンドが有る為、パスワードを入力
1. DB作成からテストデータ投入まで終了
1. `hp_diary/docker/docker-compose.yml`で`docker compose up -d`  
  4.1. Nginx用に80番ポートのみ空けておく
1. `localhost`にアクセスし、`Don't have an account?`リンクからユーザ登録画面  
  5.1. アドレスはダミー可
1. 作成ユーザの日記はまだ無い為、空の一覧画面が表示される

---
## ライセンス

このプロジェクトは MIT ライセンスの下で公開されています  
詳細は LICENSE ファイルをご確認ください

