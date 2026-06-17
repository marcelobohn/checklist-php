<?php

namespace Tests;

/**
 * Garante que o endpoint perigoso registro.limpa.php (TRUNCATE de todas as
 * tabelas) foi removido e não voltou — issue #22.
 *
 * Verificado ANONIMAMENTE de propósito: se o arquivo ainda existir, o block.php
 * responde 403 antes de qualquer execução (o teste nunca trunca dados). Se foi
 * removido, o servidor responde 404.
 */
class RegistroLimpaRemovidoTest extends FunctionalTestCase
{
    public function testEndpointDeResetFoiRemovido(): void
    {
        $r = $this->get('/registro/registro.limpa.php', $this->newJar());

        $this->assertSame(404, $r['code'],
            'O endpoint perigoso registro.limpa.php deve ter sido removido (404).');
    }
}
