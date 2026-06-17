<?php

namespace Tests;

use PHPUnit\Framework\Attributes\DataProvider;

/** Controle de acesso por sessão (block.php) — issue #4. */
class AuthGuardTest extends FunctionalTestCase
{
    public static function endpointsProtegidos(): array
    {
        return [
            'home do módulo pergunta' => ['/pergunta/'],
            'listagem AJAX'           => ['/pergunta/pergunta.view.php?acao=pesquisa&p=&pag=1'],
            'consulta de registro'    => ['/consulta/consulta.monta.php?registro=1'],
            'exclusão de modelo'      => ['/modelo/modelo.model.php?acao=apaga&id=1'],
        ];
    }

    #[DataProvider('endpointsProtegidos')]
    public function testAnonimoRecebe403(string $path): void
    {
        $r = $this->get($path, $this->newJar());
        $this->assertSame(403, $r['code'], "Anônimo não deveria acessar {$path}.");
    }

    public function testEndpointsDeLoginSaoPublicos(): void
    {
        foreach (['/', '/dlgLogin.php'] as $path) {
            $r = $this->get($path, $this->newJar());
            $this->assertSame(200, $r['code'], "{$path} deveria ser público.");
        }
    }

    public function testAnonimoNaoCriaAdmin(): void
    {
        $jar = $this->newJar(); // sem login
        $nome = 'hacker' . self::MARKER;
        $r = $this->get('/usuario/usuario.model.php', $jar, [
            'acao' => 'grava', 'idUsuario' => 0, 'nome' => $nome,
            'senha' => 'x', 'admin' => 'S',
        ]);
        $this->assertSame(403, $r['code']);
        $this->assertSame('0', self::db("SELECT COUNT(*) FROM usuario WHERE nome = '{$nome}'"));
    }
}
