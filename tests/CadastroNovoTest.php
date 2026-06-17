<?php

namespace Tests;

/**
 * Cadastro de registro NOVO com id vazio — exatamente o que o formulário envia.
 * Regressão da #15: no PHP 8, `"" != 0` é true, e o registro novo caía em
 * UPDATE (nada gravado) em vez de INSERT.
 */
class CadastroNovoTest extends FunctionalTestCase
{
    public function testNovaPerguntaComIdVazioEhInserida(): void
    {
        $jar = $this->loggedInJar();
        $desc = 'Pergunta nova' . self::MARKER;

        $this->assertSame('0', self::db("SELECT COUNT(*) FROM pergunta WHERE descricao='{$desc}'"));
        $r = $this->criaPergunta($jar, $desc); // helper envia idPergunta=''
        $this->assertSame(200, $r['code']);
        $this->assertSame('1', self::db("SELECT COUNT(*) FROM pergunta WHERE descricao='{$desc}'"),
            'Pergunta nova (id vazio) deve ser INSERIDA, não cair em UPDATE.');
    }

    public function testNovoModeloComIdVazioEhInserido(): void
    {
        $jar = $this->loggedInJar();
        $nome = 'Modelo novo' . self::MARKER;

        $r = $this->get('/modelo/modelo.model.php', $jar, [
            'acao' => 'grava', 'idModelo' => '', 'nome' => $nome, 'csrf' => $this->csrfToken($jar),
        ]);
        $this->assertSame(200, $r['code']);
        $this->assertSame('1', self::db("SELECT COUNT(*) FROM modelo WHERE nome='{$nome}'"));
    }

    public function testNovoUsuarioComIdVazioEhInserido(): void
    {
        $jar = $this->loggedInJar();
        $nome = 'usuario novo' . self::MARKER;

        $r = $this->criaUsuario($jar, $nome, 'senha123'); // helper envia idUsuario=''
        $this->assertSame(200, $r['code']);
        $this->assertSame('1', self::db("SELECT COUNT(*) FROM usuario WHERE nome='{$nome}'"));
    }
}
