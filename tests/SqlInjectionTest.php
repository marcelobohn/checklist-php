<?php

namespace Tests;

/** Prepared statements (PDO) — issue #1. */
class SqlInjectionTest extends FunctionalTestCase
{
    public function testTentativaDeBypassNoLoginFalha(): void
    {
        // Payload clássico de injeção: não pode autenticar nem causar erro 500.
        $r = $this->login($this->newJar(), "admin' OR '1'='1", 'qualquer');
        $this->assertNotSame(302, $r['code'], 'Injeção não pode burlar o login.');
        $this->assertNotSame(500, $r['code'], 'A query não deve quebrar.');
    }

    public function testAspaSimplesEhArmazenadaLiteralmente(): void
    {
        $jar = $this->loggedInJar();
        $descricao = "O'Brien " . self::MARKER; // aspa quebraria SQL concatenado

        $r = $this->criaPergunta($jar, $descricao);
        $this->assertSame(200, $r['code']);

        // Armazenada exatamente como enviada, e exatamente uma linha.
        $this->assertSame('1', self::db("SELECT COUNT(*) FROM pergunta WHERE descricao = \"{$descricao}\""));
    }
}
