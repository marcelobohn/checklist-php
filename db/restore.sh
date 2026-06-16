#!/usr/bin/env bash
# Restaura o banco a partir de um dump gerado por db/backup.sh.
#   db/restore.sh backups/checklist-AAAAMMDD-HHMMSS.sql.gz
# Aceita arquivos .sql ou .sql.gz.
set -euo pipefail
cd "$(dirname "$0")/.."

arquivo="${1:-}"
if [ -z "$arquivo" ] || [ ! -f "$arquivo" ]; then
  echo "uso: db/restore.sh <arquivo.sql|.sql.gz>"
  exit 1
fi

DB_PASSWORD="${DB_PASSWORD:-123}"
DB_NAME="${DB_NAME:-checklist}"

echo "Restaurando '$arquivo' em '$DB_NAME'... (sobrescreve os dados atuais)"
if [[ "$arquivo" == *.gz ]]; then
  gunzip -c "$arquivo" | docker compose exec -T db mysql -uroot -p"$DB_PASSWORD"
else
  docker compose exec -T db mysql -uroot -p"$DB_PASSWORD" < "$arquivo"
fi
echo "restauração concluída."
