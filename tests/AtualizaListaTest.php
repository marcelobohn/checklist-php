<?php

namespace Tests;

/**
 * Atualização da lista após incluir/remover (issue #19).
 *
 * Os links chamavam a ação AJAX e o refresh da lista na mesma linha, de forma
 * síncrona (ex.: `apagaPergunta(7,4);listaPergunta()`). Como a ação é assíncrona,
 * o refresh rodava antes de ela concluir → tela desatualizada ("nem sempre
 * funciona"). O refresh passou para o callback de sucesso do AJAX, então o HTML
 * gerado pelas listas NÃO deve mais encadear o refresh inline.
 */
class AtualizaListaTest extends FunctionalTestCase
{
    public function testListaDePerguntasDoModeloNaoEncadeiaRefreshInline(): void
    {
        $nomeM = 'Lista modelo' . self::MARKER;
        $descP = 'Lista perg' . self::MARKER;
        self::db("INSERT INTO modelo (nome) VALUES ('{$nomeM}')");
        $idM = self::db("SELECT idModelo FROM modelo WHERE nome='{$nomeM}'");
        self::db("INSERT INTO pergunta (descricao, marcar, resposta) VALUES ('{$descP}', 'S', 'N')");
        $idP = self::db("SELECT idPergunta FROM pergunta WHERE descricao='{$descP}'");
        self::db("INSERT INTO modelopergunta (idModelo, idPergunta, ordem) VALUES ({$idM}, {$idP}, 1)");

        $jar = $this->loggedInJar();
        $body = $this->get('/modelo/modelo.pergunta.lista.php', $jar, ['id' => $idM])['body'];

        // O link de remoção deve existir...
        $this->assertStringContainsString('apagaPergunta(', $body);
        // ...mas sem o refresh síncrono encadeado (ele agora roda no callback do AJAX).
        $this->assertStringNotContainsString('listaPergunta()', $body,
            'O refresh da lista não pode ser chamado inline após a ação AJAX (race).');
    }

    public function testListaDeRespostasDaPerguntaNaoEncadeiaRefreshInline(): void
    {
        $descP = 'Lista perg resp' . self::MARKER;
        self::db("INSERT INTO pergunta (descricao, marcar, resposta) VALUES ('{$descP}', 'N', 'S')");
        $idP = self::db("SELECT idPergunta FROM pergunta WHERE descricao='{$descP}'");
        self::db("INSERT INTO resposta (idPergunta, descricao) VALUES ({$idP}, 'Alt{$descP}')");

        $jar = $this->loggedInJar();
        $body = $this->get('/pergunta/pergunta.resposta.lista.php', $jar, ['id' => $idP])['body'];

        $this->assertStringContainsString('apagaResposta(', $body);
        $this->assertStringNotContainsString('listaResposta(', $body,
            'O refresh da lista não pode ser chamado inline após a ação AJAX (race).');
    }
}
