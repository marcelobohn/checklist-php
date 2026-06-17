<?php

namespace Tests;

/**
 * Integridade referencial (migration 002 — issue #16).
 *
 * CASCADE  — apagar o pai remove os filhos "donos" (resposta, modelopergunta).
 * RESTRICT — não é possível apagar pergunta/modelo usado em checklist respondido.
 *
 * Os dados são semeados direto no banco (com o marcador) e a exclusão é feita
 * pelo endpoint real (?acao=apaga). Tudo limpo no tearDown da base.
 */
class ForeignKeysTest extends FunctionalTestCase
{
    /** Insere uma linha marcada e devolve o id recém-criado (via SELECT pelo marcador). */
    private function seedPergunta(string $sufixo, string $marcar = 'N', string $resposta = 'N'): string
    {
        $desc = "FK {$sufixo}" . self::MARKER;
        self::db("INSERT INTO pergunta (descricao, marcar, resposta) VALUES ('{$desc}', '{$marcar}', '{$resposta}')");
        return self::db("SELECT idPergunta FROM pergunta WHERE descricao='{$desc}'");
    }

    private function seedModelo(string $sufixo): string
    {
        $nome = "FK {$sufixo}" . self::MARKER;
        self::db("INSERT INTO modelo (nome) VALUES ('{$nome}')");
        return self::db("SELECT idModelo FROM modelo WHERE nome='{$nome}'");
    }

    public function testCascadePerguntaApagaRespostas(): void
    {
        $idP = $this->seedPergunta('cascade-resp', 'N', 'S');
        self::db("INSERT INTO resposta (idPergunta, descricao) VALUES ({$idP}, 'Opcao A" . self::MARKER . "'), ({$idP}, 'Opcao B" . self::MARKER . "')");
        $this->assertSame('2', self::db("SELECT COUNT(*) FROM resposta WHERE idPergunta={$idP}"));

        $jar = $this->loggedInJar();
        $r = $this->get('/pergunta/pergunta.model.php', $jar, [
            'acao' => 'apaga', 'id' => $idP, 'csrf' => $this->csrfToken($jar),
        ]);
        $this->assertSame(200, $r['code']);
        $this->assertStringContainsString('Excluído com sucesso', $r['body']);
        $this->assertSame('0', self::db("SELECT COUNT(*) FROM pergunta WHERE idPergunta={$idP}"));
        $this->assertSame('0', self::db("SELECT COUNT(*) FROM resposta WHERE idPergunta={$idP}"),
            'Respostas devem sumir em cascata ao apagar a pergunta.');
    }

    public function testCascadeModeloApagaModelopergunta(): void
    {
        $idM = $this->seedModelo('cascade-mp');
        $idP = $this->seedPergunta('cascade-mp-p', 'S');
        self::db("INSERT INTO modelopergunta (idModelo, idPergunta, ordem) VALUES ({$idM}, {$idP}, 1)");
        $this->assertSame('1', self::db("SELECT COUNT(*) FROM modelopergunta WHERE idModelo={$idM}"));

        $jar = $this->loggedInJar();
        $r = $this->get('/modelo/modelo.model.php', $jar, [
            'acao' => 'apaga', 'id' => $idM, 'csrf' => $this->csrfToken($jar),
        ]);
        $this->assertSame(200, $r['code']);
        $this->assertStringContainsString('Excluído com sucesso', $r['body']);
        $this->assertSame('0', self::db("SELECT COUNT(*) FROM modelo WHERE idModelo={$idM}"));
        $this->assertSame('0', self::db("SELECT COUNT(*) FROM modelopergunta WHERE idModelo={$idM}"),
            'Associações modelopergunta devem sumir em cascata ao apagar o modelo.');
        // A pergunta NÃO some (só a associação).
        $this->assertSame('1', self::db("SELECT COUNT(*) FROM pergunta WHERE idPergunta={$idP}"));
    }

    public function testRestrictPerguntaUsadaEmRegistroNaoApaga(): void
    {
        $idM = $this->seedModelo('restrict-p-modelo');
        $idP = $this->seedPergunta('restrict-p', 'S');
        // Checklist respondido referenciando a pergunta.
        self::db("INSERT INTO registro (idModelo, usuario) VALUES ({$idM}, 'resp" . self::MARKER . "')");
        $idR = self::db("SELECT idRegistro FROM registro WHERE usuario='resp" . self::MARKER . "'");
        self::db("INSERT INTO registroitem (idRegistro, idPergunta, idResposta) VALUES ({$idR}, {$idP}, 1)");

        $jar = $this->loggedInJar();
        $r = $this->get('/pergunta/pergunta.model.php', $jar, [
            'acao' => 'apaga', 'id' => $idP, 'csrf' => $this->csrfToken($jar),
        ]);
        $this->assertSame(200, $r['code'], 'Exclusão bloqueada deve responder 200, não 500.');
        $this->assertStringContainsString('Não foi possível excluir', $r['body']);
        $this->assertSame('1', self::db("SELECT COUNT(*) FROM pergunta WHERE idPergunta={$idP}"),
            'Pergunta usada em checklist respondido NÃO pode ser apagada (RESTRICT).');
    }

    public function testRestrictModeloComRegistroNaoApaga(): void
    {
        $idM = $this->seedModelo('restrict-modelo');
        self::db("INSERT INTO registro (idModelo, usuario) VALUES ({$idM}, 'resp2" . self::MARKER . "')");

        $jar = $this->loggedInJar();
        $r = $this->get('/modelo/modelo.model.php', $jar, [
            'acao' => 'apaga', 'id' => $idM, 'csrf' => $this->csrfToken($jar),
        ]);
        $this->assertSame(200, $r['code'], 'Exclusão bloqueada deve responder 200, não 500.');
        $this->assertStringContainsString('Não foi possível excluir', $r['body']);
        $this->assertSame('1', self::db("SELECT COUNT(*) FROM modelo WHERE idModelo={$idM}"),
            'Modelo com checklist respondido NÃO pode ser apagado (RESTRICT).');
    }
}
