#!/usr/bin/env bash
# Aplica as migrations pendentes ao banco JÁ EM EXECUÇÃO (uso incremental,
# após adicionar novos arquivos em db/migrations/). No 1º up o docker/initdb.sh
# já aplica tudo; este script serve para evoluções posteriores do schema.
set -euo pipefail
cd "$(dirname "$0")/.."

DB_PASSWORD="${DB_PASSWORD:-123}"
DB_NAME="${DB_NAME:-checklist}"
dbsql() { docker compose exec -T db mysql -uroot -p"$DB_PASSWORD" "$@"; }

dbsql "$DB_NAME" -e "CREATE TABLE IF NOT EXISTS schema_migrations (
  version VARCHAR(255) NOT NULL PRIMARY KEY,
  applied_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"

aplicou=0
for f in db/migrations/*.sql; do
  version="$(basename "$f")"
  ja="$(dbsql "$DB_NAME" -N -e "SELECT COUNT(*) FROM schema_migrations WHERE version='$version';")"
  if [ "$ja" = "0" ]; then
    echo "→ aplicando $version"
    dbsql "$DB_NAME" < "$f"
    dbsql "$DB_NAME" -e "INSERT INTO schema_migrations (version) VALUES ('$version');"
    aplicou=1
  fi
done

[ "$aplicou" = "0" ] && echo "Nada a aplicar (schema atualizado)." || echo "Migrations aplicadas."
