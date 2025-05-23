#!/bin/bash

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
echo "Script is located in: $SCRIPT_DIR"

DOCKER_DIR="$SCRIPT_DIR/docker"
cp "$DOCKER_DIR/.env.sample" "$DOCKER_DIR/.env"
cp "$DOCKER_DIR/db/.env.sample" "$DOCKER_DIR/db/.env"

APP_DIR="$SCRIPT_DIR/app"
cp "$APP_DIR/.env.sample" "$APP_DIR/.env"
sudo chmod -R 0777 "$APP_DIR/storage" "$APP_DIR/bootstrap/cache"

YML_FILE="$DOCKER_DIR/docker-compose.yml"
docker compose -f "$YML_FILE" up -d --build

# コンテナ名は敢えて固定していない
# このコマンドで見つからない場合はコンテナ名を直接書く
APP_CONTAINER=$(docker ps -a --format '{{.Names}}' | grep hp_app | head -n 1)
echo "container name is $APP_CONTAINER"
if [ -z "$APP_CONTAINER" ]; then
    echo "appコンテナが見つかりません\nスクリプトにコンテナ名を書き込んで下さい"
    docker compose -f "$YML_FILE" down
    exit
fi

docker exec -ti $APP_CONTAINER composer install
docker exec -ti $APP_CONTAINER npm install
docker exec -ti $APP_CONTAINER npm run build
docker exec -ti $APP_CONTAINER php artisan storage:link
docker exec -ti $APP_CONTAINER php artisan migrate:refresh --seed

docker compose -f "$YML_FILE" down

echo "初期化処理が完了しました"
