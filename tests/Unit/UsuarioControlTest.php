<?php

namespace Tests\Unit;

use App\UsuarioControl;
use Tests\Unit\Support\FakeConexao;

class UsuarioControlTest extends UnitTestCase
{
    protected function tearDown(): void
    {
        unset($_SESSION['modo']);
    }

    private function control(FakeConexao $bd): UsuarioControl
    {
        /** @var UsuarioControl */
        return $this->comBd(UsuarioControl::class, $bd);
    }

    public function testGetListaCabecalhoAdminELinkLogado(): void
    {
        $_SESSION['modo'] = 'de';
        $bd = new FakeConexao([
            [1],
            [['idUsuario' => 2, 0 => 2, 'nome' => 'Maria', 'admin' => 'N']],
        ]);
        $html = $this->control($bd)->getLista(null, 1);

        $this->assertStringContainsString('<th>Admin</th>', $html);
        $this->assertStringContainsString('javascript:altera(2)', $html);
        $this->assertStringContainsString('Maria', $html);
    }

    public function testInserirGeraHashBcrypt(): void
    {
        $bd = new FakeConexao([[]]);
        $this->control($bd)->inserir((object) ['nome' => 'João', 'senha' => 'segredo', 'admin' => 'S']);

        $q = $bd->ultimaQuery();
        $this->assertStringContainsString('insert into usuario', $q['sql']);
        [$nome, $hash, $admin] = $q['params'];
        $this->assertSame('João', $nome);
        $this->assertSame('S', $admin);
        $this->assertNotSame('segredo', $hash, 'A senha não pode ser gravada em texto puro.');
        $this->assertTrue(password_verify('segredo', $hash), 'O hash deve validar a senha original.');
    }

    public function testAtualizarComSenhaIncluiSenhaNoUpdate(): void
    {
        $bd = new FakeConexao([[]]);
        $this->control($bd)->atualizar((object) ['nome' => 'A', 'senha' => 'nova', 'admin' => 'N', 'idUsuario' => 5]);

        $q = $bd->ultimaQuery();
        $this->assertStringContainsString('senha = ?', $q['sql']);
        $this->assertTrue(password_verify('nova', $q['params'][1]));
    }

    public function testAtualizarSemSenhaNaoTocaSenha(): void
    {
        $bd = new FakeConexao([[]]);
        $this->control($bd)->atualizar((object) ['nome' => 'A', 'senha' => '', 'admin' => 'N', 'idUsuario' => 5]);

        $q = $bd->ultimaQuery();
        $this->assertStringNotContainsString('senha = ?', $q['sql'], 'Senha vazia deve manter a atual.');
        $this->assertSame(['A', 'N', 5], $q['params']);
    }
}
