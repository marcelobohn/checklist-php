# Checklist PHP

[![CI](https://github.com/marcelobohn/checklist-php/actions/workflows/ci.yml/badge.svg)](https://github.com/marcelobohn/checklist-php/actions/workflows/ci.yml)

Sistema web de **checklists** escrito em PHP puro (~2012). Permite cadastrar
perguntas, montar modelos de checklist a partir dessas perguntas, responder os
checklists e consultar os registros respondidos.

As perguntas podem ser de dois tipos:

- **Marcar** — resposta sim/não.
- **Múltipla escolha** — alternativas de resposta cadastradas.

Características:

- Acesso a dados em **PDO com prepared statements** (PHP 8)
- Classes em `src/` com **autoload PSR-4** (Composer) e namespace `App\`
- **Integridade referencial** no schema (foreign keys CASCADE/RESTRICT)
- Telas dinâmicas com JavaScript (jQuery 3.7.1) e chamadas AJAX, com **proteção CSRF**
- *Template engine* próprio (substituição de `{tags}`)
- Suíte de testes **funcional (HTTP) + unitária** (PHPUnit) e **CI** (GitHub Actions)

> ℹ️ **Origem e modernização.** Nasceu em ~2012 em PHP puro (`mysql_*`, PHP
> 4-style, ISO-8859-1). Foi modernizado de forma incremental: **PDO + prepared
> statements, PHP 8 e UTF-8**, hash de senhas (bcrypt), credenciais por variável
> de ambiente, controle de acesso por sessão, escape anti-XSS, proteção CSRF,
> migrations/seeds, autoload PSR-4 e foreign keys. O histórico completo de
> releases e os detalhes técnicos estão em [`CLAUDE.md`](CLAUDE.md).

## Como rodar

Pré-requisito: Docker + Docker Compose. O ambiente usa
**PHP 8.3 + Apache + MySQL 5.7** (utf8mb4).

```bash
composer install            # gera vendor/ — o app usa o autoloader (PSR-4)
docker compose up -d --build
```

> ⚠️ Desde a adoção do autoload PSR-4, o app depende de `vendor/autoload.php`.
> Como o container monta o repositório, rode `composer install` **antes** do
> `docker compose up` (ou após um clone novo).

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

Dois testsuites em PHPUnit:

- **Funcional** — exercita o app dockerizado via HTTP (login/sessão, controle de
  acesso, SQL injection, hash de senha, XSS, CSRF, CRUD).
- **Unitário** — testa as classes de `src/App` em isolamento, sem app nem banco.

```bash
docker compose up -d --build   # app no ar (necessário para os funcionais)
composer install               # uma vez
composer test                  # tudo (funcional + unitário)
composer test:unit             # só unitários (rápido, sem app)
composer test:functional       # só funcionais
```

Os funcionais são **pulados** com mensagem clara se o app não estiver no ar.
Detalhes em [`tests/README.md`](tests/README.md).

## Estrutura

| Pasta        | Função                                                        |
|--------------|---------------------------------------------------------------|
| `src/`       | Classes do domínio (namespace `App\`, autoload PSR-4)         |
| `usuario/`   | Cadastro de usuários (perfil administrador)                    |
| `tests/`     | Testes funcionais e unitários (PHPUnit)                        |
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
em `db/`. No 1º `up` o banco já sobe com as migrations + seed básico (admin).

Há atalhos no Composer, no estilo `migrate:fresh` / `db:seed` do Laravel:

```bash
composer db:fresh                  # recria o banco do zero (drop + migrations + seed básico)
composer db:fresh-seed             # idem + seed de desenvolvimento (perguntas/modelo)
composer db:seed dev               # só aplica um seed (basic|dev)
composer db:backup                 # gera backups/checklist-<data>.sql.gz
composer db:restore backups/<arq>  # restaura de um backup
```

> ⚠️ `db:fresh` / `db:fresh-seed` / `db:reset` são **destrutivos** (recriam o
> banco do zero). Por baixo, esses atalhos chamam os scripts de `db/`
> (`fresh.sh`, `seed.sh`, `migrate.sh`, `backup.sh`, `restore.sh`), que também
> podem ser usados diretamente.

Os dados vivem no volume Docker `dbdata` (não versionado). Detalhes em
[`CLAUDE.md`](CLAUDE.md#banco-de-dados).
