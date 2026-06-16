#!/usr/bin/env bash
# Gera um backup do banco (mysqldump comprimido) em backups/.
#   db/backup.sh            -> backups/checklist-AAAAMMDD-HHMMSS.sql.gz
set -euo pipefail
cd "$(dirname "$0")/.."

DB_PASSWORD="${DB_PASSWORD:-123}"
DB_NAME="${DB_NAME:-checklist}"

mkdir -p backups
ts="$(date +%Y%m%d-%H%M%S)"
out="backups/${DB_NAME}-${ts}.sql.gz"

docker compose exec -T db mysqldump -uroot -p"$DB_PASSWORD" \
  --databases "$DB_NAME" --add-drop-table --single-transaction | gzip > "$out"

echo "backup criado: $out ($(du -h "$out" | cut -f1))"
