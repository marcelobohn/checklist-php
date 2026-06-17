<?php

namespace Tests\Unit;

use App\PerguntaControl;
use Tests\Unit\Support\FakeConexao;
use Tests\Unit\Support\ThrowingConexao;

class PerguntaControlTest extends UnitTestCase
{
    protected function tearDown(): void
    {
        unset($_SESSION['perfil']);
    }

    private function control(FakeConexao $bd): PerguntaControl
    {
        /** @var PerguntaControl */
        return $this->comBd(PerguntaControl::class, $bd);
    }

    public function testGetListaPaginaERodape(): void
    {
        // 25 registros -> 3 páginas; página atual 1 não vira link.
        $bd = new FakeConexao([
            [25],                                  // count(*)
            [['idPergunta' => 1, 0 => 1, 'descricao' => 'A', 'marcar' => 'S', 'resposta' => 'N']],
        ]);
        $html = $this->control($bd)->getLista(null, 1);

        $this->assertStringContainsString('| Registros: 25', $html);
        $this->assertStringContainsString('javascript:lista(2)', $html);
        $this->assertStringContainsString('javascript:lista(3)', $html);
        $this->assertStringNotContainsString('javascript:lista(1)', $html, 'A página atual não deve ser link.');
    }

    public function testGetListaCabecalhosDaTabela(): void
    {
        $bd = new FakeConexao([
            [1],
            [['idPergunta' => 7, 0 => 7, 'descricao' => 'X', 'marcar' => 'S', 'resposta' => 'N']],
        ]);
        $html = $this->control($bd)->getLista(null, 1);

        $this->assertStringContainsString('<th>Descricao</th>', $html);
        $this->assertStringContainsString('<th>Marca</th>', $html);
        $this->assertStringContainsString('<th>Resposta</th>', $html);
    }

    public function testGetListaEscapaDescricaoEAdminVeLinkAltera(): void
    {
        $_SESSION['perfil'] = 'adm';
        $bd = new FakeConexao([
            [1],
            [['idPergunta' => 9, 0 => 9, 'descricao' => 'Café <b>', 'marcar' => 'N', 'resposta' => 'S']],
        ]);
        $html = $this->control($bd)->getLista(null, 1);

        $this->assertStringContainsString('Café &lt;b&gt;', $html, 'A descrição deve ser escapada (XSS).');
        $this->assertStringContainsString('javascript:altera(9)', $html, 'Admin vê o link de edição.');
    }

    public function testGetListaVaziaMostraMensagem(): void
    {
        $bd = new FakeConexao([[0], []]);
        $html = $this->control($bd)->getLista(null, 1);

        $this->assertSame('Não foi encontrado nenhum dado.', $html);
    }

    public function testGetListaComBuscaUsaLikeNaDescricao(): void
    {
        $bd = new FakeConexao([[0], []]);
        $this->control($bd)->getLista('abc', 1);

        // 1ª query é o count, com o filtro por descricao.
        $count = $bd->queries[0];
        $this->assertStringContainsString('descricao like ?', $count['sql']);
        $this->assertSame(['abc%'], $count['params']);
    }

    public function testInserirMontaInsertComParametros(): void
    {
        $bd = new FakeConexao([[]]);
        $model = (object) ['descricao' => 'Nova', 'marcar' => 'S', 'resposta' => 'N'];

        $this->assertTrue($this->control($bd)->inserir($model));
        $q = $bd->ultimaQuery();
        $this->assertStringContainsString('insert into pergunta', $q['sql']);
        $this->assertSame(['Nova', 'S', 'N'], $q['params']);
    }

    public function testAtualizarMontaUpdateComId(): void
    {
        $bd = new FakeConexao([[]]);
        $model = (object) ['descricao' => 'Edit', 'marcar' => 'N', 'resposta' => 'S', 'idPergunta' => 42];

        $this->assertTrue($this->control($bd)->atualizar($model));
        $q = $bd->ultimaQuery();
        $this->assertStringContainsString('update pergunta set', $q['sql']);
        $this->assertSame(['Edit', 'N', 'S', 42], $q['params']);
    }

    public function testApagarSucesso(): void
    {
        $bd = new FakeConexao([[]]);
        $this->assertTrue($this->control($bd)->apagar(5));
        $this->assertStringContainsString('delete from pergunta', $bd->ultimaQuery()['sql']);
    }

    public function testApagarDevolveFalseEmPdoException(): void
    {
        /** @var PerguntaControl $control */
        $control = $this->comBd(PerguntaControl::class, new ThrowingConexao());
        $this->assertFalse($control->apagar(5), 'FK RESTRICT: apagar em uso não deve estourar, e sim devolver false.');
    }
}
