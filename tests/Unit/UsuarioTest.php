<?php

namespace Tests\Unit;

use App\Usuario;
use Tests\Unit\Support\FakeConexao;

class UsuarioTest extends UnitTestCase
{
    public function testSettersEGetters(): void
    {
        /** @var Usuario $u */
        $u = $this->comBd(Usuario::class, new FakeConexao());
        $u->setIdUsuario(8);
        $u->setNome('Ana');
        $u->setAdmin('S');

        $this->assertSame(8, $u->getIdUsuario());
        $this->assertSame('Ana', $u->getNome());
        $this->assertSame('S', $u->getAdmin());
    }

    public function testSetUsuarioCarregaQuandoEncontra(): void
    {
        $bd = new FakeConexao([
            [['idUsuario' => 1, 'nome' => 'admin', 'senha' => 'hash', 'admin' => 'S']],
        ]);
        /** @var Usuario $u */
        $u = $this->comBd(Usuario::class, $bd);

        $this->assertTrue($u->setUsuario(1));
        $this->assertSame('admin', $u->getNome());
        $this->assertSame('S', $u->getAdmin());
    }

    public function testSetUsuarioDevolveFalseQuandoNaoEncontra(): void
    {
        /** @var Usuario $u */
        $u = $this->comBd(Usuario::class, new FakeConexao([[]]));
        $this->assertFalse($u->setUsuario(999));
    }
}
