# Checklist PHP — Documentação

Sistema web de **checklists** em PHP. Permite cadastrar perguntas, montar modelos
de checklist a partir dessas perguntas, responder os checklists e consultar os
registros respondidos.

> ℹ️ **Origem.** O projeto nasceu em ~2012 em PHP puro (API `mysql_*`, PHP 4-style,
> ISO-8859-1). Em 2026 o acesso a dados foi **migrado para PDO com prepared
> statements**, o runtime subiu para **PHP 8** e tudo passou a **UTF-8**. O
> histórico legado ainda aparece na estrutura (MVC manual, uma pasta por módulo).

## Como rodar (Docker)

Ambiente: **PHP 8.3 + Apache + MySQL 5.7** (utf8mb4).

```bash
composer install           # gera vendor/ — o app usa o autoloader (PSR-4) em runtime
docker compose up -d --build
```

> ℹ️ Desde a adoção do **autoload PSR-4** (classes em `src/App`), o app depende de
> `vendor/autoload.php`. Como o container monta o repositório (`./`), rode
> `composer install` **antes** do `docker compose up` (ou após um clone novo).

- App:   <http://localhost/checklist/>  ⚠️ o caminho `/checklist/` é obrigatório
- Login: **admin / admin** (seed básico — ver [Banco de dados](#banco-de-dados))
- Banco: exposto no host em `localhost:3307` (root / `123`), opcional para debug

> A URL base é fixa em `template/lateral.php`
> (`$EnderecoBase = "http://localhost:80/checklist/"`) e há um anti-hotlink
> (`block.php`) que valida o `SERVER_NAME`. Por isso o projeto é montado no
> subdiretório `checklist/` e servido na **porta 80**. Acessar pela raiz
> (`http://localhost/`) ou por outra porta quebra a navegação.

Para parar / resetar:

```bash
docker compose down          # para os containers
docker compose down -v       # para e APAGA o banco (migrations + seed básico no próximo up)
```

### Conexão com o banco

`src/ConexaoBD.php` lê as credenciais de **variáveis de ambiente** (`getenv`), com
defaults para o ambiente Docker de desenvolvimento:

| Variável      | Default     | Observação                          |
|---------------|-------------|-------------------------------------|
| `DB_HOST`     | `localhost` | `localhost` no PDO usa socket Unix  |
| `DB_PORT`     | `3306`      |                                     |
| `DB_NAME`     | `checklist` |                                     |
| `DB_USER`     | `root`      |                                     |
| `DB_PASSWORD` | `123`       | fonte única no compose (MySQL + web)|

Para usar outras credenciais, exporte as variáveis (ou crie um `.env`) antes do
`docker compose up` — ex.: `DB_PASSWORD=minhasenha docker compose up -d`. O
`docker-compose.yml` injeta `${DB_PASSWORD:-123}` tanto no MySQL quanto no `web`,
mantendo os dois em sincronia. Veja `.env.example`.

Como `DB_HOST=localhost` usa **socket Unix**, o `docker-compose.yml` compartilha o
diretório do socket (`/var/run/mysqld`) entre `db` e `web`, e o `docker/Dockerfile`
aponta `pdo_mysql.default_socket` para esse caminho.

## Testes

Dois testsuites em PHPUnit:

- **Funcional** (`tests/`): bate no app dockerizado via HTTP — login/sessão,
  controle de acesso (403), SQL injection, hash de senha, XSS, CSRF e os fluxos
  de CRUD. Dados criados levam o marcador `~TEST~` e são limpos no `tearDown`
  (idempotente, seed preservado). Se o app não estiver no ar, esses testes são
  **pulados** (skip) com uma mensagem clara.
- **Unitário** (`tests/Unit/`): testa as classes de `src/App` em **isolamento**,
  sem app nem banco — injeta um *fake* de `ConexaoBD` (via reflection) e cobre
  `h()`, a paginação/render do `getLista`, o SQL de `inserir`/`atualizar`/`apagar`,
  os getters/setters das entidades e o `Dispatcher` de ações.

```bash
docker compose up -d --build   # app no ar (para os funcionais)
composer install               # dependências de teste (uma vez)
composer test                  # tudo (funcional + unitário)
composer test:unit             # só unitários (rápido, sem app)
composer test:functional       # só funcionais
```

Detalhes em [`tests/README.md`](tests/README.md).

## Arquitetura

PHP procedural/MVC manual, **uma pasta por funcionalidade**. Cada módulo repete o
mesmo trio de arquivos (o passo-a-passo de criação está em `novo cadastro.txt`):

| Arquivo                | Papel                                                               |
|------------------------|---------------------------------------------------------------------|
| `<mod>/config.php`     | Define `$Titulo`, `$ArquivoJS`, `$Aplicativo` (roteamento)          |
| `<mod>/index.php`      | Página do módulo, montada pelo template engine                      |
| `<mod>/<mod>.model.php`| registra os handlers de `?acao=grava` / `?acao=apaga` no `App\Dispatcher` (usa as classes de `src/App`) |
| `<mod>/<mod>.view.php` | HTML do formulário/listagem                                         |
| `js/<mod>.js`          | Front-end: chamadas AJAX (jQuery 3.7.1) para os arquivos acima      |

> As **classes** (entidade + control) vivem em `src/App` e são carregadas por
> **autoload PSR-4** (Composer) — ver [Classes e autoload](#classes-e-autoload-psr-4).
> A entidade `App\<Mod>` e o `App\<Mod>Control` (CRUD/SQL: insert, update, delete,
> `getLista` paginada) substituem os antigos `<mod>.model.php`/`<mod>.control.php`.

### Módulos

| Pasta        | Função                                                                    |
|--------------|---------------------------------------------------------------------------|
| `usuario/`   | CRUD de usuários (somente perfil `adm`)                                    |
| `pergunta/`  | Cadastro de perguntas e suas respostas (sim/não ou múltipla escolha)      |
| `modelo/`    | Monta um modelo associando perguntas + ordem (`modelopergunta`)           |
| `registro/`  | Responde um checklist a partir de um modelo e grava em `registro(item)`   |
| `consulta/`  | Filtra e exibe checklists já respondidos                                   |

### Classes e autoload (PSR-4)

As classes ficam em `src/`, namespace `App\`, carregadas por **autoload do Composer**
(`composer.json` → `autoload.psr-4: {"App\\":"src/"}`); a função global `h()` vem de
`src/helpers.php` (autoload `files`). Não há mais `include_once` manual de classe.

| Classe | Arquivo |
|--------|---------|
| `App\ConexaoBD` | `src/ConexaoBD.php` — PDO. Ver [Acesso a dados](#acesso-a-dados-pdo) |
| `App\TemplateParser` | `src/TemplateParser.php` — *template engine* (`{Tag}` via `ob_start`/`include`) |
| `App\Dispatcher` | `src/Dispatcher.php` — roteador simples de `?acao=`: `on('acao', handler)` + `run($acao)`; ação ausente/desconhecida é no-op. Usado nos `<mod>.model.php` |
| `App\BaseControl` | `src/BaseControl.php` — base abstrata: `getLista` paginado + `renderTabela()` (template method) |
| `App\Pergunta` / `App\PerguntaControl` | `src/Pergunta.php` / `src/PerguntaControl.php` (control estende `BaseControl`) |
| `App\Modelo` / `App\ModeloControl` | `src/Modelo.php` / `src/ModeloControl.php` (control estende `BaseControl`) |
| `App\Usuario` / `App\UsuarioControl` | `src/Usuario.php` / `src/UsuarioControl.php` (control estende `BaseControl`) |

O autoloader é carregado nos bootstraps: `block.php` (endpoints autenticados),
`template/start.php` (páginas de índice) e `login.php`. Dentro do `namespace App`,
classes globais usam `\` (ex.: `\PDO`, `\PDOException`).

Todos os arquivos de `src/` usam **`declare(strict_types=1)`** com tipagem
**gradual**: tipos de retorno (`: bool`/`: string`/`: void`/`: \PDOStatement`) e
de parâmetro nos métodos internos seguros. Setters que recebem dados crus de
`$_REQUEST` (string, às vezes vazia) e a propriedade `$bd` (injetada por um fake
nos testes) permanecem **sem tipo**, de propósito.

### Infraestrutura comum (raiz)

- `template/start.php` — carrega o autoloader, `session_start()`, inclui `config.php` e monta `$head` (CSS/JS).
- `template/modelo.php` / `template/acesso.php` — layout logado / tela de login.
- `template/lateral.php` — menu lateral (mostra itens conforme `$_SESSION['perfil']`).
- `index.php` — raiz: mostra login ou home conforme `$_SESSION['modo']`.
- `login.php` / `logout.php` / `dlgLogin.php` — autenticação por sessão.
- `block.php` — **guard de autenticação por sessão**: incluído no topo dos entry points
  (index de cada módulo + endpoints AJAX/ação); retorna **403** se não houver login (`$_SESSION['modo']!='de'`).
- `csrf.php` — **validação de token CSRF**: incluída nos endpoints de escrita (grava/apaga/inclui/limpa);
  compara `$_REQUEST['csrf']` com `$_SESSION['csrf']` (gerado em `start.php`) e retorna **403** se inválido.
  O token é anexado automaticamente às requisições AJAX por um `$.ajaxPrefilter` (ver `template/start.php`).

### Front-end e CSS

Um único arquivo **`css/style.css`**, carregado em `template/start.php` com
cache-busting (`?v=filemtime`, via `$asset`). Foi revisado em etapas (uma issue
por mudança) e hoje está **limpo, responsivo e temável**:

- **Responsivo** — `<meta name="viewport">` nos templates, `box-sizing: border-box`
  global e uma media query (`max-width: 700px`) que empilha o layout de duas
  colunas (menu lateral + conteúdo) em **coluna única** no celular.
- **Cores por variáveis** — bloco `:root` com `--cor-*` e `--fonte-base`
  (esta termina em `sans-serif`); nada de literais de cor espalhados.
- **Sem `style=` inline nas views** — larguras de campo viraram classes
  (`.campo` 400px, `.campo-medio` 200px, `.rotulo-fixo` 180px, `.sem-marcador`,
  `.respostas-titulo`), aplicadas nas `*.view.php`, no `registro/` e nas classes
  `*Control`.
- ⚠️ **Caveat — layout por JS:** `js/util.js` tem `ajustaTela()`, um ajuste
  herdado que define **largura/altura inline** de `#divCorpo`/`#divConteudo` no
  desktop (estilo inline vence o CSS). Abaixo de 700px ele se **desativa** e
  devolve o controle ao CSS. Por isso, mexer na largura/altura desses dois
  elementos passa por `ajustaTela()`, não só pelo `.css`.

> Histórico da revisão: regras mortas (#30), fundação responsiva (#31), layout
> mobile (#32), inline → classes (#33), variáveis de cor (#34). São mudanças de
> front-end sem versão de release (não entram na tabela abaixo).

### Acesso a dados (PDO)

Toda query passa pela classe `App\ConexaoBD`. O método central é:

```php
$bd = new \App\ConexaoBD();   // ou: use App\ConexaoBD; $bd = new ConexaoBD();
// SELECT com parâmetros (prepared statement):
$stmt = $bd->query("select * from usuario where nome = ?", array($nome));
$r    = $stmt->fetch();          // uma linha
$rows = $stmt->fetchAll();       // todas as linhas (itere com foreach)
$n    = $bd->query("select count(*) from pergunta")->fetchColumn();
// INSERT/UPDATE/DELETE: passe os valores em $params; id novo via lastInsertId:
$bd->query("insert into modelo (nome) values (?)", array($nome));
$novoId = $bd->con->lastInsertId();
```

Regras seguidas na migração:
- **Sempre prepared statements** — valores de `$_POST`/`$_REQUEST` vão em `$params`, nunca concatenados.
- O fetch padrão é `PDO::FETCH_BOTH` (acesso por índice **e** por nome), preservando o código que usava `$r[0]` e `$r['nome']`.
- `LIMIT` usa inteiros via `(int)` (placeholders de LIMIT não são suportados sem emulação).
- Erros lançam `PDOException` (`ERRMODE_EXCEPTION`); inclusões/exclusões pontuais tratam com `try/catch`.

### Fluxo de uso

1. **Login** (`login.php`) grava `$_SESSION['usuario'|'modo'|'perfil']`.
2. **Pergunta**: cadastra perguntas; tipo `marcar='S'` (sim/não) ou `resposta='S'`
   (alternativas na tabela `resposta`).
3. **Modelo**: cria um modelo e adiciona perguntas a ele (`modelopergunta.ordem`).
4. **Registro**: escolhe um modelo → `registro.monta.php` gera o formulário →
   `registro.grava.php` insere em `registro` + `registroitem` (id via `lastInsertId`).
5. **Consulta**: filtra por cliente/tarefa e exibe o checklist respondido.

## Banco de dados

Banco `checklist` (MySQL, **utf8mb4**). O `.sql` original não existia; o schema foi
**reconstruído a partir das queries**. O ciclo de vida do banco fica em `db/`:

```
db/
  migrations/001_init_schema.sql   # DDL versionado (tracking em schema_migrations)
  seeds/basic.sql                  # essencial: usuário admin (auto, no 1º up)
  seeds/dev.sql                    # exemplos: perguntas/modelo (opt-in)
  migrate.sh   fresh.sh   seed.sh   backup.sh   restore.sh
```

- **1º `up`** (banco novo): `docker/initdb.sh` aplica as migrations e o seed básico.
- **Migrations futuras**: adicione `db/migrations/NNN_*.sql` e rode `./db/migrate.sh`.
- **Exemplos de dev**: `./db/seed.sh dev` (e `./db/seed.sh basic` para o essencial).
- **Backup/restore**: `./db/backup.sh` (gera `backups/*.sql.gz`) e `./db/restore.sh <arquivo>`.

### Atalhos estilo Laravel (Composer)

Equivalentes a `migrate:fresh` / `db:seed`, operando no banco dockerizado:

```bash
composer db:fresh         # dropa tudo + reaplica migrations + seed básico (admin)
composer db:fresh-seed    # idem + seed de desenvolvimento (perguntas/modelo)
composer db:reset         # alias de db:fresh
composer db:seed dev      # só aplica um seed (basic|dev) — equivale a ./db/seed.sh
composer db:backup        # gera backups/checklist-AAAAMMDD-HHMMSS.sql.gz
composer db:restore backups/<arquivo>.sql.gz   # restaura de um dump
```

> ⚠️ `db:fresh*` / `db:reset` são **destrutivos** (recriam o banco do zero). Rode
> `composer db:backup` antes se precisar preservar os dados. Por baixo, esses
> atalhos apenas chamam os scripts de `db/` (`fresh.sh`/`seed.sh`/`backup.sh`/`restore.sh`).

> Os dados vivem no volume Docker `dbdata` (não versionado; apagado por
> `docker compose down -v`). Só a **definição** (migrations/seeds) está no git.

| Tabela           | Colunas-chave                                                            |
|------------------|--------------------------------------------------------------------------|
| `usuario`        | idUsuario, nome, senha (hash bcrypt), admin (S/N)                         |
| `pergunta`       | idPergunta, descricao, marcar (S/N), resposta (S/N)                       |
| `resposta`       | idResposta, idPergunta, descricao                                        |
| `modelo`         | idModelo, nome                                                            |
| `modelopergunta` | idModelo, idPergunta, ordem                                              |
| `registro`       | idRegistro, idModelo, rand, data, usuario, versao, base, tarefa, codCliente |
| `registroitem`   | idRegistro, idPergunta, idResposta                                       |

> `registro.rand` era usado para recuperar o `idRegistro` recém-inserido via
> `SELECT` (gambiarra pré-PDO). Após a migração o id vem de `lastInsertId()`; a
> coluna foi mantida por compatibilidade do schema.

> **Foreign keys** (migration `002`): `resposta`, `modelopergunta` e
> `registroitem`→`registro` usam **CASCADE** (filhos somem com o pai);
> `registro`→`modelo` e `registroitem`→`pergunta` usam **RESTRICT** (não se
> apaga modelo/pergunta com checklist respondido). `registroitem.idResposta`
> **não** tem FK: para perguntas `marcar='S'` guarda a sentinela `1`/`0`, não um
> `idResposta` real. O `apagar()` de pergunta/modelo trata a violação RESTRICT e
> responde uma mensagem amigável (sem 500).

## Segurança / dívida técnica

✅ **Corrigido na migração para PDO/PHP 8:**
- **SQL Injection** — todas as queries usam prepared statements.
- **`mysql_*`** (removida no PHP 7) → PDO; roda em PHP 8 suportado.
- **Construtores PHP 4 / `&new`** → `__construct()` / `new`.
- **ISO-8859-1 + `utf8_encode/decode`** (deprecados) → UTF-8 ponta a ponta.
- **Senhas em texto puro** → hash bcrypt (`password_hash` / `password_verify`); editar usuário sem informar senha mantém a atual.
- **Credenciais fixas** no fonte → lidas de variáveis de ambiente (`DB_*`); ver [Conexão com o banco](#conexão-com-o-banco).
- **`block.php`** (anti-hotlink por `HTTP_REFERER`, ilusório) → **guard de sessão** (403) aplicado a todos os entry points; endpoints anônimos passam a ser bloqueados.
- **Escape de saída (XSS)** → todo dado de **texto livre** vindo do banco/usuário é
  impresso via o helper `h()` (`htmlspecialchars` com `ENT_QUOTES`), definido em
  `src/helpers.php`. **Exceção documentada:** valores **estruturalmente seguros** são
  impressos crus de propósito — **IDs inteiros** (`AUTO_INCREMENT`: `$r[0]`,
  `getIdUsuario()`, `idPergunta`…) e **flags `S`/`N`** (`marcar`, `resposta`, `admin`).
  Um auto-increment nunca contém `<`/`"` e as flags só guardam `S`/`N`, então não há
  superfície de XSS. Regra prática: **todo campo de texto digitável passa por `h()`**.
- **CSRF** → token por sessão (`start.php`) anexado a todo AJAX via `$.ajaxPrefilter` e validado nos endpoints de escrita (`csrf.php`).

Não há mais itens de dívida técnica de segurança pendentes do levantamento inicial.
O endpoint perigoso `registro.limpa.php` (truncate de todas as tabelas) foi
**removido** (junto com o helper `ConexaoBD::limpaTabela()`).

## Histórico de releases

Modernização rastreada por issues e releases (uma tarefa por mudança). Detalhes
de cada release em <https://github.com/marcelobohn/checklist-php/releases>.

| Versão | Entrega | Issue |
|--------|---------|-------|
| `v1.0.0` | Documentação inicial + ambiente Docker (PHP 5.6) | — |
| `v1.1.0` | Migração `mysql_*` → **PDO** com prepared statements, **PHP 8**, **UTF-8** | #1 |
| `v1.1.1` | **Hash de senhas** (bcrypt: `password_hash`/`password_verify`) | #2 |
| `v1.1.2` | **Credenciais** do banco via variáveis de ambiente (`DB_*`) | #3 |
| `v1.1.3` | **Controle de acesso** por sessão (substitui o anti-hotlink frágil) | #4 |
| `v1.1.4` | **Escape de saída** anti-XSS (helper `h()`) | #5 |
| `v1.1.5` | **Proteção CSRF** nos endpoints de escrita | #6 |
| `v1.2.0` | **Suíte de testes** funcionais (PHPUnit) | #7 |
| `v1.3.0` | **Migrations, seeds** (básico/dev) e **backup/restore** | #8 |
| `v1.4.0` | **CI** (GitHub Actions: `composer test` a cada push) | #9 |
| `v1.4.1` | Fix do `Warning` de acesso anônimo no `index.php` | #10 |
| `v1.4.2` | Remoção de **arquivos mortos** do projeto | #13 |
| `v1.5.0` | Atualização do **jQuery** (1.7.1 → 3.7.1) | #14 |
| `v1.5.1` | Fix: **cadastro novo** falhava no PHP 8 (id vazio caía em UPDATE) | #15 |
| `v1.6.0` | **Foreign keys** no schema (migration `002`, CASCADE/RESTRICT) | #16 |
| `v1.6.1` | Fix: incluir/remover pergunta no modelo (XHR cru ia sem CSRF → 403) | #17 |
| `v1.6.2` | **Cache-busting** de assets (`?v=filemtime`) — correções de JS/CSS chegam ao usuário | #18 |
| `v1.6.3` | Fix: lista de pergunta/resposta não atualizava (refresh rodava antes do AJAX — race) | #19 |
| `v1.7.0` | **Autoload PSR-4**: classes em `src/App`, fim dos `include_once` manuais | #20 |
| `v1.7.1` | **BaseControl**: `getLista` paginado extraído para classe base (dedup) | #21 |
| `v1.7.2` | **Remoção** do endpoint perigoso `registro.limpa.php` (truncate total) | #22 |
| `v1.8.0` | Comandos `composer db:fresh` / `db:fresh-seed` / `db:seed` (estilo Laravel) | #23 |
| `v1.8.1` | Atalhos `composer db:backup` / `db:restore` / `db:reset` (alias) | #24 |
| `v1.9.0` | **Testes unitários** das classes de `src/App` (fake de `ConexaoBD`, sem app/DB) | #25 |
| `v1.9.1` | CI: bump `actions/checkout` v4 → v5 (silencia aviso de Node 20) | #26 |
| `v1.9.2` | **Tipagem gradual** (`declare(strict_types=1)` + type hints) em `src/App` | #28 |
