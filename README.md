# Checklist PHP

Sistema web de **checklists** escrito em PHP puro (~2012). Permite cadastrar
perguntas, montar modelos de checklist a partir dessas perguntas, responder os
checklists e consultar os registros respondidos.

As perguntas podem ser de dois tipos:

- **Marcar** — resposta sim/não.
- **Múltipla escolha** — alternativas de resposta cadastradas.

Características:

- Telas dinâmicas com JavaScript (jQuery 1.7.1) e chamadas AJAX
- *Template engine* próprio (substituição de `{tags}`)
- Organização MVC manual, uma pasta por funcionalidade

> ⚠️ **Projeto legado.** Usa a API `mysql_*` (removida no PHP 7), construtores
> no estilo PHP 4 e charset ISO-8859-1, além de vulnerabilidades conhecidas
> (SQL injection, senha em texto puro). Veja [`CLAUDE.md`](CLAUDE.md) para a
> documentação técnica completa e a lista de dívida técnica. O ambiente Docker
> abaixo serve para rodar o projeto **como ele era originalmente**, sem alterar
> o código-fonte.

## Como rodar

Pré-requisito: Docker + Docker Compose. O ambiente reproduz a stack da época
(PHP 5.6 + Apache + MySQL 5.7) sem modificar o fonte.

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

## Estrutura

| Pasta        | Função                                                        |
|--------------|---------------------------------------------------------------|
| `usuario/`   | Cadastro de usuários (perfil administrador)                    |
| `pergunta/`  | Cadastro de perguntas e suas respostas                        |
| `modelo/`    | Monta modelos de checklist associando perguntas               |
| `registro/`  | Responde um checklist a partir de um modelo                   |
| `consulta/`  | Filtra e exibe checklists já respondidos                      |
| `template/`  | Layout, sessão e *template engine*                            |
| `js/`        | Front-end (jQuery + scripts por módulo)                       |
| `docker/`    | `Dockerfile` e `schema.sql` para o ambiente local             |

## Banco de dados

O `.sql` original não existia no repositório; o schema foi reconstruído a partir
das queries do código e está em [`docker/schema.sql`](docker/schema.sql), que o
container do MySQL carrega automaticamente na primeira execução (com um usuário
`admin` e dados de exemplo).
