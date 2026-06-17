#!/usr/bin/env bash
# Recria o banco do zero (estilo `php artisan migrate:fresh`): dropa o schema,
# recria o database e reaplica TODAS as migrations. Aplica o seed básico (admin).
# Com --seed, também carrega o seed de desenvolvimento (perguntas/modelo).
#
#   db/fresh.sh           -> drop + create + migrations + seed básico
#   db/fresh.sh --seed    -> idem + seed dev
#
# Destrutivo: apaga TODOS os dados. Faça um db/backup.sh antes se precisar.
set -euo pipefail
cd "$(dirname "$0")/.."

DB_PASSWORD="${DB_PASSWORD:-123}"
DB_NAME="${DB_NAME:-checklist}"
dbexec() { docker compose exec -T db mysql -uroot -p"$DB_PASSWORD" "$@"; }

echo "→ recriando o banco '$DB_NAME' (drop + create)..."
dbexec -e "DROP DATABASE IF EXISTS \`$DB_NAME\`; CREATE DATABASE \`$DB_NAME\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

echo "→ aplicando migrations..."
./db/migrate.sh

echo "→ aplicando seed básico (admin)..."
dbexec "$DB_NAME" < db/seeds/basic.sql
echo "seed 'basic' aplicado."

if [ "${1:-}" = "--seed" ]; then
  ./db/seed.sh dev
fi

echo "banco '$DB_NAME' recriado do zero."
