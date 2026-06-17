<?php

namespace Tests;

/**
 * Caracterização da listagem paginada (getLista) dos três módulos — issue #21.
 *
 * Trava a saída atual (cabeçalhos da tabela, link de edição `altera(`, rodapé
 * "Registros:" e mensagem de lista vazia) para garantir que a extração do
 * BaseControl não muda o comportamento.
 *
 * A listagem é renderizada pelo endpoint <mod>.view.php?acao=pesquisa, que
 * chama $control->getLista($p,$pag).
 */
class ListagemControlTest extends FunctionalTestCase
{
    private function pesquisa(string $modulo, string $jar, string $p): string
    {
        return $this->get("/{$modulo}/{$modulo}.view.php", $jar, [
            'acao' => 'pesquisa', 'p' => $p, 'pag' => '1',
        ])['body'];
    }

    public function testListaModeloCabecalhoLinkERodape(): void
    {
        $nome = 'ZZ' . self::MARKER . ' modelo';
        self::db("INSERT INTO modelo (nome) VALUES ('{$nome}')");

        $jar = $this->loggedInJar();
        $body = $this->pesquisa('modelo', $jar, 'ZZ');

        $this->assertStringContainsString('<th>Nome</th>', $body);
        $this->assertStringContainsString('Registros:', $body);
        $this->assertStringContainsString('altera(', $body, 'Admin deve ver o link de edição.');
        $this->assertStringContainsString($nome, $body);
    }

    public function testListaPerguntaCabecalhos(): void
    {
        $desc = 'ZZ' . self::MARKER . ' pergunta';
        self::db("INSERT INTO pergunta (descricao, marcar, resposta) VALUES ('{$desc}', 'S', 'N')");

        $jar = $this->loggedInJar();
        $body = $this->pesquisa('pergunta', $jar, 'ZZ');

        $this->assertStringContainsString('<th>Descricao</th>', $body);
        $this->assertStringContainsString('<th>Marca</th>', $body);
        $this->assertStringContainsString('<th>Resposta</th>', $body);
        $this->assertStringContainsString('Registros:', $body);
        $this->assertStringContainsString($desc, $body);
    }

    public function testListaUsuarioCabecalhos(): void
    {
        $nome = 'ZZ' . self::MARKER . ' usuario';
        self::db("INSERT INTO usuario (nome, senha, admin) VALUES ('{$nome}', 'x', 'N')");

        $jar = $this->loggedInJar();
        $body = $this->pesquisa('usuario', $jar, 'ZZ');

        $this->assertStringContainsString('<th>Admin</th>', $body);
        $this->assertStringContainsString('Registros:', $body);
        $this->assertStringContainsString($nome, $body);
    }

    public function testListaVaziaMostraMensagem(): void
    {
        $jar = $this->loggedInJar();
        $body = $this->pesquisa('modelo', $jar, 'ZZNAOEXISTE' . self::MARKER);

        $this->assertStringContainsString('Não foi encontrado nenhum dado.', $body);
    }
}
