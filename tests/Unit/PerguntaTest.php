<?php

namespace Tests\Unit;

use App\Pergunta;
use Tests\Unit\Support\FakeConexao;

class PerguntaTest extends UnitTestCase
{
    private function pergunta(FakeConexao $bd): Pergunta
    {
        /** @var Pergunta */
        return $this->comBd(Pergunta::class, $bd);
    }

    public function testSettersEGetters(): void
    {
        $p = $this->pergunta(new FakeConexao());
        $p->setIdPergunta(7);
        $p->setDescricao('Ambiente testado?');
        $p->setMarcar('S');
        $p->setResposta('N');

        $this->assertSame(7, $p->getIdPergunta());
        $this->assertSame('Ambiente testado?', $p->getDescricao());
        $this->assertSame('S', $p->getMarcar());
        $this->assertSame('N', $p->getResposta());
    }

    public function testSetPerguntaCarregaQuandoEncontra(): void
    {
        $bd = new FakeConexao([
            [['idPergunta' => 3, 'descricao' => 'Desc', 'marcar' => 'S', 'resposta' => 'N']],
        ]);
        $p = $this->pergunta($bd);

        $this->assertTrue($p->setPergunta(3));
        $this->assertSame(3, $p->getIdPergunta());
        $this->assertSame('Desc', $p->getDescricao());
        $this->assertStringContainsString('from pergunta where idPergunta = ?', $bd->ultimaQuery()['sql']);
        $this->assertSame([3], $bd->ultimaQuery()['params']);
    }

    public function testSetPerguntaDevolveFalseQuandoNaoEncontra(): void
    {
        $p = $this->pergunta(new FakeConexao([[]])); // fetch() -> false
        $this->assertFalse($p->setPergunta(999));
    }
}
