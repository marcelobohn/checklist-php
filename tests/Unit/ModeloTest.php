<?php

namespace Tests\Unit;

use App\Modelo;
use Tests\Unit\Support\FakeConexao;

class ModeloTest extends UnitTestCase
{
    public function testSettersEGetters(): void
    {
        /** @var Modelo $m */
        $m = $this->comBd(Modelo::class, new FakeConexao());
        $m->setIdModelo(4);
        $m->setNome('Checklist X');

        $this->assertSame(4, $m->getIdModelo());
        $this->assertSame('Checklist X', $m->getNome());
    }

    public function testSetModeloCarregaQuandoEncontra(): void
    {
        $bd = new FakeConexao([[['idModelo' => 2, 'nome' => 'Y']]]);
        /** @var Modelo $m */
        $m = $this->comBd(Modelo::class, $bd);

        $this->assertTrue($m->setModelo(2));
        $this->assertSame('Y', $m->getNome());
        $this->assertSame([2], $bd->ultimaQuery()['params']);
    }

    public function testSetModeloDevolveFalseQuandoNaoEncontra(): void
    {
        /** @var Modelo $m */
        $m = $this->comBd(Modelo::class, new FakeConexao([[]]));
        $this->assertFalse($m->setModelo(999));
    }
}
