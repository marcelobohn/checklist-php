<?php

namespace Tests;

/**
 * Cache-busting de assets (issue #18).
 *
 * Os assets (JS/CSS) eram referenciados sem versão, então o browser mantinha
 * a versão em cache e correções de front (ex.: #17) não chegavam ao usuário.
 * Cada asset deve ser servido com um parâmetro de versão (?v=<filemtime>).
 */
class AssetCacheBustingTest extends FunctionalTestCase
{
    public function testJsDoModuloTemVersao(): void
    {
        $jar = $this->loggedInJar();
        $body = $this->get('/modelo/', $jar)['body'];

        $this->assertMatchesRegularExpression(
            '#js/modelo\.js\?v=\d+#',
            $body,
            'O JS do módulo deve ter ?v=<versão> para invalidar o cache do browser.'
        );
    }

    public function testCssTemVersao(): void
    {
        $jar = $this->loggedInJar();
        $body = $this->get('/modelo/', $jar)['body'];

        $this->assertMatchesRegularExpression(
            '#css/style\.css\?v=\d+#',
            $body,
            'O CSS deve ter ?v=<versão> para invalidar o cache do browser.'
        );
    }

    public function testJqueryTemVersao(): void
    {
        $jar = $this->loggedInJar();
        $body = $this->get('/pergunta/', $jar)['body'];

        $this->assertMatchesRegularExpression(
            '#js/jquery-3\.7\.1\.min\.js\?v=\d+#',
            $body,
            'Os assets compartilhados também devem ter versão.'
        );
    }
}
