<?php

namespace Tests;

/** Hash de senhas (bcrypt) — issue #2. */
class PasswordHashTest extends FunctionalTestCase
{
    public function testSeedAdminEhHashBcrypt(): void
    {
        $this->assertSame('$2y$', self::db("SELECT LEFT(senha,4) FROM usuario WHERE nome='admin'"));
    }

    public function testSenhaErradaRejeitada(): void
    {
        $r = $this->login($this->newJar(), 'admin', 'nao-e-a-senha');
        $this->assertNotSame(302, $r['code']);
    }

    public function testNovoUsuarioTemSenhaHasheada(): void
    {
        $jar = $this->loggedInJar();
        $nome = 'maria' . self::MARKER;

        $r = $this->criaUsuario($jar, $nome, 'segredo');
        $this->assertSame(200, $r['code']);

        $this->assertSame('$2y$', self::db("SELECT LEFT(senha,4) FROM usuario WHERE nome='{$nome}'"),
            'A senha do novo usuário deve ser gravada como hash.');

        // E o login com a senha em claro deve funcionar (password_verify).
        $login = $this->login($this->newJar(), $nome, 'segredo');
        $this->assertSame(302, $login['code']);
    }

    public function testEdicaoSemSenhaMantemAtual(): void
    {
        $jar = $this->loggedInJar();
        $nome = 'jose' . self::MARKER;
        $this->criaUsuario($jar, $nome, 'minhasenha');

        $id   = self::db("SELECT idUsuario FROM usuario WHERE nome='{$nome}'");
        $hash = self::db("SELECT senha FROM usuario WHERE nome='{$nome}'");

        // Edita promovendo a admin, SEM informar senha.
        $r = $this->get('/usuario/usuario.model.php', $jar, [
            'acao' => 'grava', 'idUsuario' => $id, 'nome' => $nome,
            'senha' => '', 'admin' => 'S', 'csrf' => $this->csrfToken($jar),
        ]);
        $this->assertSame(200, $r['code']);

        $this->assertSame('S', self::db("SELECT admin FROM usuario WHERE idUsuario={$id}"));
        $this->assertSame($hash, self::db("SELECT senha FROM usuario WHERE idUsuario={$id}"),
            'Editar sem senha não pode alterar o hash.');
        $this->assertSame(302, $this->login($this->newJar(), $nome, 'minhasenha')['code']);
    }
}
