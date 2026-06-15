# Checklist PHP — Documentação

Sistema web de **checklists** em PHP puro, de ~2012. Permite cadastrar perguntas,
montar modelos de checklist a partir dessas perguntas, responder os checklists e
consultar os registros respondidos.

> ⚠️ **Código legado.** Usa a API `mysql_*` (removida no PHP 7), construtores no
> estilo PHP 4, charset ISO-8859-1 e tem vulnerabilidades sérias (ver
> [Segurança](#segurança--dívida-técnica)). O objetivo desta documentação é
> entender e **rodar o projeto como ele era originalmente**, não modernizá-lo.

## Como rodar (Docker)

Reproduz o ambiente da época (PHP 5.6 + MySQL 5.7) **sem alterar o fonte**:

```bash
docker compose up -d --build
```

- App:   <http://localhost/checklist/>  ⚠️ o caminho `/checklist/` é obrigatório
- Login: **admin / admin** (criado pelo seed em `docker/schema.sql`)
- Banco: exposto no host em `localhost:3307` (root / `123`), opcional para debug

> O código tem a URL base fixa em `template/lateral.php`
> (`$EnderecoBase = "http://localhost:80/checklist/"`) e um anti-hotlink
> (`block.php`) que valida o `SERVER_NAME`. Por isso o projeto é montado no
> subdiretório `checklist/` e servido na **porta 80** — assim todos os links
> do menu funcionam sem alterar o fonte. Acessar pela raiz (`http://localhost/`)
> ou por outra porta quebra a navegação.

Para parar / resetar:

```bash
docker compose down          # para os containers
docker compose down -v       # para e APAGA o banco (recria o schema no próximo up)
```

### Por que funciona sem mexer no código

`conexaoBD.php` tem o host do banco fixo em `mysql_connect('localhost:3306', 'root', '123')`.
Na API `mysql_*`, o host `localhost` significa conexão via **socket Unix** (a porta é
ignorada) — como era no servidor LAMP único original. Por isso o `docker-compose.yml`
compartilha o diretório do socket do MySQL (`/var/run/mysqld`) entre os containers `db`
e `web` num volume, e o `docker/Dockerfile` aponta `mysql.default_socket` para esse
caminho. Assim o PHP encontra o banco em `localhost` sem alterar nenhuma linha do fonte.

## Arquitetura

PHP procedural/MVC manual, **uma pasta por funcionalidade**. Cada módulo repete o
mesmo trio de arquivos (o passo-a-passo de criação está em `novo cadastro.txt`):

| Arquivo                | Papel                                                               |
|------------------------|---------------------------------------------------------------------|
| `<mod>/config.php`     | Define `$Titulo`, `$ArquivoJS`, `$Aplicativo` (roteamento)          |
| `<mod>/index.php`      | Página do módulo, montada pelo template engine                      |
| `<mod>/<mod>.model.php`| Classe-entidade + *dispatcher* de `?acao=grava` / `?acao=apaga`     |
| `<mod>/<mod>.control.php`| CRUD/SQL (insert, update, delete, getLista paginada)              |
| `<mod>/<mod>.view.php` | HTML do formulário/listagem                                         |
| `js/<mod>.js`          | Front-end: chamadas AJAX (jQuery 1.7.1) para os arquivos acima      |

### Módulos

| Pasta        | Função                                                                    |
|--------------|---------------------------------------------------------------------------|
| `usuario/`   | CRUD de usuários (somente perfil `adm`)                                    |
| `pergunta/`  | Cadastro de perguntas e suas respostas (sim/não ou múltipla escolha)      |
| `modelo/`    | Monta um modelo associando perguntas + ordem (`modelopergunta`)           |
| `registro/`  | Responde um checklist a partir de um modelo e grava em `registro(item)`   |
| `consulta/`  | Filtra e exibe checklists já respondidos                                   |

### Infraestrutura comum (raiz)

- `conexaoBD.php` — classe `conexaoBD`, abre/fecha conexão MySQL (credenciais fixas).
- `template/class.template.php` — *template engine* minimalista: substitui `{Tag}`
  por texto ou pelo conteúdo de um arquivo (via `ob_start`/`include`).
- `template/start.php` — `session_start()`, inclui `config.php` e monta `$head` (CSS/JS).
- `template/modelo.php` / `template/acesso.php` — layout logado / tela de login.
- `template/lateral.php` — menu lateral (mostra itens conforme `$_SESSION['perfil']`).
- `index.php` — raiz: mostra login ou home conforme `$_SESSION['modo']`.
- `login.php` / `logout.php` / `dlgLogin.php` — autenticação por sessão.
- `block.php` — *anti-hotlink* via `HTTP_REFERER` (frágil; incluído no `template/modelo.php`).

### Fluxo de uso

1. **Login** (`login.php`) grava `$_SESSION['usuario'|'modo'|'perfil']`.
2. **Pergunta**: cadastra perguntas; tipo `marcar='S'` (sim/não) ou `resposta='S'`
   (alternativas na tabela `resposta`).
3. **Modelo**: cria um modelo e adiciona perguntas a ele (`modelopergunta.ordem`).
4. **Registro**: escolhe um modelo → `registro.monta.php` gera o formulário →
   `registro.grava.php` insere em `registro` + `registroitem`.
5. **Consulta**: filtra por cliente/tarefa e exibe o checklist respondido.

## Banco de dados

Banco `checklist` (MySQL, latin1). O `.sql` original não existia; o schema foi
**reconstruído a partir das queries** e está documentado em `docker/schema.sql`.

| Tabela           | Colunas-chave                                                            |
|------------------|--------------------------------------------------------------------------|
| `usuario`        | idUsuario, nome, senha (texto puro), admin (S/N)                          |
| `pergunta`       | idPergunta, descricao, marcar (S/N), resposta (S/N)                       |
| `resposta`       | idResposta, idPergunta, descricao                                        |
| `modelo`         | idModelo, nome                                                            |
| `modelopergunta` | idModelo, idPergunta, ordem                                              |
| `registro`       | idRegistro, idModelo, rand, data, usuario, versao, base, tarefa, codCliente |
| `registroitem`   | idRegistro, idPergunta, idResposta                                       |

`registro.rand` é um número aleatório gravado para depois recuperar o `idRegistro`
recém-inserido com um `SELECT` (gambiarra no lugar de `mysql_insert_id()`).

## Segurança / dívida técnica

Conhecido e **não corrigido de propósito** (rodando "como está"):

- **SQL Injection** em praticamente todas as queries (concatenação de `$_POST`/`$_REQUEST`).
- **Senhas em texto puro**; comparação direta em `login.php`.
- **Credenciais fixas** no fonte (`conexaoBD.php`: root / `123`).
- **`mysql_*`** removida no PHP 7 → exige PHP 5.6 (por isso o Docker).
- **`&new ...`** e construtores PHP 4 → *fatal error* em PHP 7+.
- **Charset ISO-8859-1** com `utf8_encode/decode` espalhados (mojibake latin1↔utf8).
- **`block.php`** depende de `HTTP_REFERER` (forjável/ausente) com `preg_match` mal formado.

Para modernizar (migração `mysql_*`→PDO, *prepared statements*, hash de senha,
UTF-8), tratar como reescrita à parte — fora do escopo de "rodar como está".
