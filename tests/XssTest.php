<?php

namespace Tests;

/** Escape de saída (anti-XSS) — issue #5. */
class XssTest extends FunctionalTestCase
{
    public function testPayloadScriptEhEscapadoNaListagem(): void
    {
        $jar = $this->loggedInJar();
        $payload = '<script>alert(1)</script>';
        $this->criaPergunta($jar, $payload . self::MARKER);

        // Armazenado cru no banco...
        $this->assertSame('1', self::db(
            "SELECT COUNT(*) FROM pergunta WHERE descricao LIKE '%<script>alert(1)</script>%'"
        ));

        // ...mas renderizado escapado, sem <script> cru na saída.
        $body = $this->get('/pergunta/pergunta.view.php', $jar,
            ['acao' => 'pesquisa', 'p' => '', 'pag' => 1])['body'];

        $this->assertStringContainsString('&lt;script&gt;alert(1)&lt;/script&gt;', $body);
        $this->assertStringNotContainsString('<script>alert(1)</script>', $body);
    }
}
