# Notas de arquitetura e história

Registro histórico sobre o projeto original — PHP puro de ~2012, sem framework,
usando apenas **jQuery** para facilitar a UI. Escrito após a leitura completa do
código durante a modernização (ver [Histórico de releases](CLAUDE.md#histórico-de-releases)).

## O que o projeto tem de notável

### 1. Um "mini-framework" construído do zero, com disciplina
Sem Laravel/Symfony, o autor reinventou as peças essenciais por conta própria:

- **Template engine próprio** (`template/class.template.php`, ~40 linhas):
  substituição de `{Tags}` por texto/arquivo, com o truque de
  `ob_start()` / `include` / `ob_get_contents()` para renderizar sub-templates
  PHP em string. Há até uma distinção sutil: o template principal é lido com
  `file_get_contents` (não executa PHP) e os sub-templates via `include`
  (executam).
- **MVC manual por convenção**: uma pasta por funcionalidade, sempre com o mesmo
  quarteto `*.model.php` / `*.control.php` / `*.view.php` / `config.php`. Na
  prática, um *scaffolding* feito à mão.

### 2. O `novo cadastro.txt`
Um arquivo de texto com o passo-a-passo para criar um módulo novo
("copiar pasta, renomear, corrigir títulos...") e até um "problemas no primeiro
teste". Um *code generator* manual e documentado. Com uma ironia gostosa: um app
de checklist cuja própria criação foi documentada como um checklist.

### 3. O modelo de domínio é genuinamente bom
Perguntas **reutilizáveis** → modelos que **compõem** perguntas com ordem →
formulário **gerado dinamicamente** conforme o tipo (`marcar` = sim/não,
`resposta` = múltipla escolha da tabela `resposta`) → respostas gravadas. O
mapeamento `name="r_<idPergunta>"` para remontar as respostas na gravação é
limpo. Prova da solidez do design: o schema foi **reconstruído só a partir das
queries** e bateu perfeitamente — um modelo de dados que sobreviveu à
modernização sem mudar uma tabela.

### 4. Praticamente uma SPA em 2012
A navegação dentro dos módulos é via `jQuery.load()` em `divs`, sem recarregar a
página — antes de React/Vue. Há até `iphone-style-checkboxes.js` e o "ajuste
dinâmico da tela". O autor se importava com UX.

### 5. As gambiarras contam uma história
O truque do `rand` (gravar um número aleatório e depois
`SELECT ... WHERE rand=...` para recuperar o id, no lugar de `mysql_insert_id()`)
é tecnicamente errado, mas revela criatividade resolvendo problema com o que se
sabia. Os experimentos comentados (`crossUrlDecode`, testes de UTF-8 em
`pergunta.model`) são traços honestos de aprendizado e iteração.

## Por que a modernização foi tranquila

A migração para PDO + prepared statements + PHP 8 foi surpreendentemente
**mecânica** — justamente por causa da disciplina original. Como tudo passava por
`conexaoBD` e todo módulo seguia o mesmo padrão, trocar a camada de dados virou
uma transformação repetível. A organização que o autor se impôs há ~14 anos
rendeu juros na hora de modernizar.

## Em uma frase

Os **ossos** do projeto (arquitetura, modelo de domínio, intenção de UX) eram de
alguém com bom instinto de engenharia; o que estava datado era a **carne** da
época (`mysql_*`, segurança, ISO-8859-1) — exatamente o que se conserta. É um bom
exemplo de "primeiros princípios": mostra, na prática, tudo o que um framework
faz por você quando você não tem um.
