#!/bin/bash
# Executado pelo entrypoint do MySQL APENAS na primeira inicialização do banco
# (volume vazio). Aplica as migrations (registrando em schema_migrations) e o
# seed básico. O seed de desenvolvimento é opt-in (db/seed.sh dev).
set -e

DB="${MYSQL_DATABASE:-checklist}"
run() { mysql -uroot -p"${MYSQL_ROOT_PASSWORD}" "$@"; }

echo "[initdb] criando tabela de controle de migrations..."
run "$DB" -e "CREATE TABLE IF NOT EXISTS schema_migrations (
  version VARCHAR(255) NOT NULL PRIMARY KEY,
  applied_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"

for f in /db/migrations/*.sql; do
  version="$(basename "$f")"
  echo "[initdb] aplicando migration $version"
  run "$DB" < "$f"
  run "$DB" -e "INSERT INTO schema_migrations (version) VALUES ('$version');"
done

echo "[initdb] aplicando seed básico"
run "$DB" < /db/seeds/basic.sql

echo "[initdb] concluído."
