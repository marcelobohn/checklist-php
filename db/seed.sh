#!/usr/bin/env bash
# Aplica um seed ao banco em execução.
#   db/seed.sh basic   -> dados essenciais (usuário admin)
#   db/seed.sh dev      -> dados de exemplo (perguntas/modelo)
set -euo pipefail
cd "$(dirname "$0")/.."

qual="${1:-}"
if [ -z "$qual" ]; then
  echo "uso: db/seed.sh [basic|dev]"
  exit 1
fi
arquivo="db/seeds/${qual}.sql"
if [ ! -f "$arquivo" ]; then
  echo "seed não encontrado: $arquivo"
  exit 1
fi

DB_PASSWORD="${DB_PASSWORD:-123}"
DB_NAME="${DB_NAME:-checklist}"
docker compose exec -T db mysql -uroot -p"$DB_PASSWORD" "$DB_NAME" < "$arquivo"
echo "seed '$qual' aplicado."
