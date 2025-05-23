FROM mysql:8.0

ENV MYSQL_ROOT_PASSWORD root_password
ENV TZ "Asia/Tokyo"

RUN microdnf update -y \
    && microdnf install -y glibc-locale-source \
    && localedef -i ja_JP -c -f UTF-8 -A /usr/share/locale/locale.alias ja_JP.UTF-8

ENV LANG ja_JP.UTF-8
ENV LC_ALL ja_JP.UTF-8

ADD my.cnf /etc/my.cnf

# 初期化用のSQLとスクリプトをコンテナにコピー
COPY ./init.sh /docker-entrypoint-initdb.d/
COPY ./init.sql /docker-entrypoint-initdb.d/
