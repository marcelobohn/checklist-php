<?php

namespace Tests;

/** Proteção CSRF nos endpoints de escrita — issue #6. */
class CsrfTest extends FunctionalTestCase
{
    public function testEscritaSemTokenEhBloqueada(): void
    {
        $jar = $this->loggedInJar();
        $descricao = 'SemToken' . self::MARKER;

        $r = $this->get('/pergunta/pergunta.model.php', $jar, [
            'acao' => 'grava', 'idPergunta' => 0, 'descricao' => $descricao,
            'marcar' => 'S', 'resposta' => 'N', // sem 'csrf'
        ]);

        $this->assertSame(403, $r['code']);
        $this->assertSame('0', self::db("SELECT COUNT(*) FROM pergunta WHERE descricao='{$descricao}'"),
            'Sem token CSRF nada pode ser gravado.');
    }

    public function testEscritaComTokenFunciona(): void
    {
        $jar = $this->loggedInJar();
        $descricao = 'ComToken' . self::MARKER;

        $r = $this->criaPergunta($jar, $descricao); // inclui o token
        $this->assertSame(200, $r['code']);
        $this->assertSame('1', self::db("SELECT COUNT(*) FROM pergunta WHERE descricao='{$descricao}'"));
    }
}
