#!/bin/bash
# init.sh: 環境変数を使ってinit.sqlをカスタマイズする
# MySQLの初期化プロセスがこのファイルを実行する
# shスクリプトはsqlファイルより実行順が早いので置換後にSQLが実行される

# SQLファイルを置換して変数を埋め込む
sed -e "s/\${MYSQL_DATABASE}/${MYSQL_DATABASE}/g" \
    -e "s/\${MYSQL_USER}/${MYSQL_USER}/g" \
    -e "s/\${MYSQL_PASSWORD}/${MYSQL_PASSWORD}/g" \
    /docker-entrypoint-initdb.d/init.sql

