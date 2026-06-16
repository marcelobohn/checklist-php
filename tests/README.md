# Testes automatizados

Suíte de testes **funcionais** (PHPUnit) que exercita o app real (dockerizado)
de ponta a ponta via HTTP, cobrindo funcionalidade e as proteções de segurança
adicionadas na modernização.

## Como rodar

1. Suba o ambiente (com o banco no estado de seed):
   ```bash
   docker compose up -d --build
   ```
2. Instale as dependências de teste (uma vez):
   ```bash
   composer install
   ```
3. Rode a suíte:
   ```bash
   composer test
   # ou: ./vendor/bin/phpunit --testdox
   ```

> Os testes batem em `http://localhost/checklist` por padrão. Para outro
> endereço, exporte `BASE_URL`. A senha do MySQL usada nas asserções/limpeza
> vem de `DB_PASSWORD` (default `123`).

## O que é coberto

| Arquivo                 | Cobertura | Issue |
|-------------------------|-----------|-------|
| `SmokeTest`             | Login, sessão, credenciais inválidas | — |
| `AuthGuardTest`         | Acesso anônimo bloqueado (403); páginas de login públicas | #4 |
| `SqlInjectionTest`      | Prepared statements (bypass de login falha; aspa literal) | #1 |
| `PasswordHashTest`      | Hash bcrypt; login; editar sem senha mantém a atual | #2 |
| `XssTest`               | Payload `<script>` renderizado escapado | #5 |
| `CsrfTest`              | Escrita sem token bloqueada; com token funciona | #6 |

## Como funciona

- `FunctionalTestCase` provê o cliente HTTP (curl com cookie jar por teste),
  `login()`, `csrfToken()` e o helper `db()` (consulta/limpeza via
  `docker compose exec db mysql`).
- Dados criados nos testes levam o marcador `~TEST~` e são removidos
  automaticamente no `tearDown`, mantendo o seed intacto e a suíte idempotente.
