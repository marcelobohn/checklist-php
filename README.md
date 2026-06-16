# Checklist PHP

Sistema web de **checklists** escrito em PHP puro (~2012). Permite cadastrar
perguntas, montar modelos de checklist a partir dessas perguntas, responder os
checklists e consultar os registros respondidos.

As perguntas podem ser de dois tipos:

- **Marcar** — resposta sim/não.
- **Múltipla escolha** — alternativas de resposta cadastradas.

Características:

- Acesso a dados em **PDO com prepared statements** (PHP 8)
- Telas dinâmicas com JavaScript (jQuery 1.7.1) e chamadas AJAX
- *Template engine* próprio (substituição de `{tags}`)
- Organização MVC manual, uma pasta por funcionalidade

> ℹ️ **Origem e migração.** Nasceu em ~2012 em PHP puro (`mysql_*`, PHP 4-style,
> ISO-8859-1). Foi **migrado para PDO + prepared statements, PHP 8 e UTF-8**,
> corrigindo SQL injection e as APIs removidas. Detalhes técnicos e dívida
> técnica restante em [`CLAUDE.md`](CLAUDE.md).

## Como rodar

Pré-requisito: Docker + Docker Compose. O ambiente usa
**PHP 8.3 + Apache + MySQL 5.7** (utf8mb4).

```bash
docker compose up -d --build
```

Depois acesse:

- **App:** <http://localhost/checklist/> &nbsp;(o caminho `/checklist/` é obrigatório)
- **Login:** `admin` / `admin`

Parar / resetar:

```bash
docker compose down       # para os containers
docker compose down -v    # para e APAGA o banco (recria o schema no próximo up)
```

> O projeto é servido no subdiretório `/checklist/` na porta **80** porque a URL
> base e o anti-hotlink estão fixos no código (`template/lateral.php`,
> `block.php`). Acessar por outra porta ou pela raiz quebra a navegação.
> Detalhes em [`CLAUDE.md`](CLAUDE.md).

## Testes

Suíte funcional (PHPUnit) que exercita o app dockerizado via HTTP, cobrindo
login/sessão, controle de acesso, SQL injection, hash de senha, XSS e CSRF:

```bash
docker compose up -d --build   # app no ar (com seed)
composer install               # uma vez
composer test                  # roda a suíte
```

Detalhes em [`tests/README.md`](tests/README.md).

## Estrutura

| Pasta        | Função                                                        |
|--------------|---------------------------------------------------------------|
| `usuario/`   | Cadastro de usuários (perfil administrador)                    |
| `tests/`     | Suíte de testes funcionais (PHPUnit)                           |
| `pergunta/`  | Cadastro de perguntas e suas respostas                        |
| `modelo/`    | Monta modelos de checklist associando perguntas               |
| `registro/`  | Responde um checklist a partir de um modelo                   |
| `consulta/`  | Filtra e exibe checklists já respondidos                      |
| `template/`  | Layout, sessão e *template engine*                            |
| `js/`        | Front-end (jQuery + scripts por módulo)                       |
| `db/`        | Migrations, seeds e scripts de backup/restore                 |
| `docker/`    | `Dockerfile` e `initdb.sh` (inicialização do banco)           |

## Banco de dados

O schema (reconstruído das queries do código) e o ciclo de vida do banco ficam
em `db/`:

```bash
# no 1º "up" o banco já sobe com as migrations + seed básico (usuário admin)
./db/seed.sh dev          # carrega dados de exemplo (perguntas/modelo)
./db/migrate.sh           # aplica migrations pendentes (db/migrations/NNN_*.sql)
./db/backup.sh            # gera backups/checklist-<data>.sql.gz
./db/restore.sh <arquivo> # restaura de um backup
```

Os dados vivem no volume Docker `dbdata` (não versionado). Detalhes em
[`CLAUDE.md`](CLAUDE.md#banco-de-dados).
