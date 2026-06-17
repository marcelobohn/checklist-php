<?php

namespace Tests;

use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Guarda de regressão da #17.
 *
 * A proteção CSRF (#6) anexa o token via jQuery.ajaxPrefilter — só funciona para
 * AJAX feito pelo jQuery. Chamadas via XMLHttpRequest cru NÃO passam pelo
 * prefilter e vão sem token (→ 403). Portanto os scripts dos módulos que fazem
 * escrita não podem usar XMLHttpRequest cru: toda chamada deve ir pelo jQuery.
 */
class AssetsCsrfTest extends FunctionalTestCase
{
    public static function scriptsProvider(): array
    {
        return [
            'modelo.js'   => [__DIR__ . '/../js/modelo.js'],
            'pergunta.js' => [__DIR__ . '/../js/pergunta.js'],
        ];
    }

    #[DataProvider('scriptsProvider')]
    public function testNaoUsaXmlHttpRequestCru(string $arquivo): void
    {
        $this->assertFileExists($arquivo);
        $src = (string) file_get_contents($arquivo);
        $this->assertStringNotContainsString('XMLHttpRequest', $src,
            'AJAX deve passar pelo jQuery (ajaxPrefilter anexa o CSRF); XHR cru vai sem token e toma 403.');
        $this->assertStringNotContainsString('ActiveXObject', $src,
            'Resíduo de IE antigo; usar jQuery.');
    }
}
