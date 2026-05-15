#!/usr/bin/env bash
set -euo pipefail

DEPLOY_HOST="${DEPLOY_HOST:-atlantic-server.com}"
DEPLOY_USER="${DEPLOY_USER:-mugiew}"
DEPLOY_PATH="${DEPLOY_PATH:-/var/www/mugiewblog}"
BRANCH="${BRANCH:-main}"

ssh "${DEPLOY_USER}@${DEPLOY_HOST}" bash -s -- "${DEPLOY_PATH}" "${BRANCH}" <<'REMOTE'
set -euo pipefail

deploy_path="$1"
branch="$2"

cd "$deploy_path"

git fetch origin "$branch"
git checkout "$branch"
git pull --ff-only origin "$branch"

docker compose up -d --build --remove-orphans
docker compose exec -T app php artisan migrate --force
docker compose exec -T app php artisan storage:link
docker compose exec -T app php artisan optimize:clear
docker compose exec -T app php artisan config:cache
docker compose exec -T app php artisan route:cache
docker compose exec -T app php artisan view:cache
docker compose exec -T app php artisan event:cache
docker compose exec -T app php artisan schedule:interrupt || true
docker compose exec -T horizon php artisan horizon:terminate || true

docker compose ps
REMOTE
