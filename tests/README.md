# Testes automatizados

Dois testsuites em PHPUnit:

- **funcional** (`tests/`): exercita o app real (dockerizado) de ponta a ponta
  via HTTP, cobrindo funcionalidade e as proteções de segurança da modernização.
- **unitário** (`tests/Unit/`): testa as classes de `src/App` em isolamento, sem
  app nem banco (injeta um *fake* de `ConexaoBD`).

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
   composer test              # tudo (funcional + unitário)
   composer test:unit         # só unitários (rápido, NÃO precisa do app)
   composer test:functional   # só funcionais
   ```

> Os testes **funcionais** são pulados (skip) com uma mensagem clara se o app
> não estiver acessível; os **unitários** rodam sempre.

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
| `IncluiPerguntaModeloTest` / `AtualizaListaTest` | Incluir/remover pergunta no modelo (CSRF + refresh) | #17, #19 |
| `ForeignKeysTest`       | Integridade referencial (CASCADE/RESTRICT) | #16 |
| `Unit/*` (unitário)     | Classes de `src/App`: `h()`, `getLista`, CRUD, entidades | #25 |

## Como funciona

- `FunctionalTestCase` provê o cliente HTTP (curl com cookie jar por teste),
  `login()`, `csrfToken()` e o helper `db()` (consulta/limpeza via
  `docker compose exec db mysql`).
- Dados criados nos testes levam o marcador `~TEST~` e são removidos
  automaticamente no `tearDown`, mantendo o seed intacto e a suíte idempotente.
