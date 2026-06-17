<?php

namespace Tests\Unit;

use App\ModeloControl;
use Tests\Unit\Support\FakeConexao;
use Tests\Unit\Support\ThrowingConexao;

class ModeloControlTest extends UnitTestCase
{
    private function control(FakeConexao $bd): ModeloControl
    {
        /** @var ModeloControl */
        return $this->comBd(ModeloControl::class, $bd);
    }

    public function testGetListaCabecalhoNome(): void
    {
        $bd = new FakeConexao([
            [1],
            [['idModelo' => 3, 0 => 3, 'nome' => 'Modelo X']],
        ]);
        $html = $this->control($bd)->getLista(null, 1);

        $this->assertStringContainsString('<th>Nome</th>', $html);
        $this->assertStringContainsString('Modelo X', $html);
        $this->assertStringContainsString('| Registros: 1', $html);
    }

    public function testGetListaPerguntaSeparaDisponiveisESelecionadas(): void
    {
        // 1ª query: perguntas disponíveis; 2ª: perguntas já no modelo.
        $bd = new FakeConexao([
            [['idPergunta' => 10, 'descricao' => 'Disp']],
            [['idPergunta' => 20, 'descricao' => 'Sel']],
        ]);
        // getListaPergunta ecoa um "</ul>" direto (quirk legado); captura para
        // não vazar na saída do teste.
        ob_start();
        $html = $this->control($bd)->getListaPergunta(1);
        ob_end_clean();

        $this->assertStringContainsString('Perguntas diponíveis', $html);
        $this->assertStringContainsString('incluiPergunta(idModelo.value, idPergunta.value)', $html);
        $this->assertStringContainsString('apagaPergunta(1,20)', $html);
    }

    public function testInserirMontaInsert(): void
    {
        $bd = new FakeConexao([[]]);
        $this->control($bd)->inserir((object) ['nome' => 'Novo']);
        $q = $bd->ultimaQuery();
        $this->assertStringContainsString('insert into modelo', $q['sql']);
        $this->assertSame(['Novo'], $q['params']);
    }

    public function testApagarDevolveFalseEmPdoException(): void
    {
        /** @var ModeloControl $control */
        $control = $this->comBd(ModeloControl::class, new ThrowingConexao());
        $this->assertFalse($control->apagar(1));
    }
}
