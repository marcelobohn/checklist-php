<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Helper de escape de saída h() (src/helpers.php) — prevenção de XSS.
 */
class HelpersTest extends TestCase
{
    public function testEscapaTagsHtml(): void
    {
        $this->assertSame('&lt;script&gt;alert(1)&lt;/script&gt;', h('<script>alert(1)</script>'));
    }

    public function testEscapaAspasSimplesEDuplas(): void
    {
        // ENT_QUOTES escapa as duas.
        $this->assertSame('&quot;a&#039;b&quot;', h('"a\'b"'));
    }

    public function testTextoSemEspeciaisFicaIntacto(): void
    {
        $this->assertSame('Olá mundo', h('Olá mundo'));
    }

    public function testConverteValoresNaoString(): void
    {
        $this->assertSame('123', h(123));
        $this->assertSame('', h(null));
    }
}
