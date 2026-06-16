<?php

namespace Tests;

/** Conectividade básica e autenticação. */
class SmokeTest extends FunctionalTestCase
{
    public function testPaginaDeLoginEhPublica(): void
    {
        $r = $this->get('/', $this->newJar());
        $this->assertSame(200, $r['code']);
        $this->assertStringContainsString('frmLogin', $r['body'], 'A tela de login deveria renderizar.');
        $this->assertStringNotContainsString('Acesso negado', $r['body'], 'Não deveria ser a página de 403.');
    }

    public function testLoginValidoRedireciona(): void
    {
        $r = $this->login($this->newJar());
        $this->assertSame(302, $r['code'], 'admin/admin deveria autenticar.');
    }

    public function testLoginInvalidoEhRejeitado(): void
    {
        $r = $this->login($this->newJar(), 'admin', 'senha-errada');
        $this->assertNotSame(302, $r['code'], 'Senha errada não pode autenticar.');
        $this->assertStringContainsString('Verifique', $r['body']);
    }
}
