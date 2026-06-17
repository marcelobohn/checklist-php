<?php

namespace Tests;

/**
 * Incluir/remover pergunta no modelo e resposta na pergunta (issue #17).
 *
 * Estes endpoints são protegidos por CSRF (csrf.php). O bug: o front os chamava
 * via XMLHttpRequest cru, sem passar pelo ajaxPrefilter que anexa o token, então
 * iam sem CSRF e tomavam 403 (a operação nunca acontecia).
 *
 * Aqui exercitamos o contrato HTTP: sem token → 403; com token → grava/apaga.
 * A guarda de que o front usa jQuery (e portanto anexa o CSRF) está em
 * AssetsCsrfTest.
 */
class IncluiPerguntaModeloTest extends FunctionalTestCase
{
    private function seedPergunta(string $sufixo, string $marcar = 'S', string $resposta = 'N'): string
    {
        $desc = "Inc {$sufixo}" . self::MARKER;
        self::db("INSERT INTO pergunta (descricao, marcar, resposta) VALUES ('{$desc}', '{$marcar}', '{$resposta}')");
        return self::db("SELECT idPergunta FROM pergunta WHERE descricao='{$desc}'");
    }

    private function seedModelo(string $sufixo): string
    {
        $nome = "Inc {$sufixo}" . self::MARKER;
        self::db("INSERT INTO modelo (nome) VALUES ('{$nome}')");
        return self::db("SELECT idModelo FROM modelo WHERE nome='{$nome}'");
    }

    public function testIncluirPerguntaNoModeloExigeCsrf(): void
    {
        $idM = $this->seedModelo('csrf-modelo');
        $idP = $this->seedPergunta('csrf-perg');

        $jar = $this->loggedInJar();
        // SEM token → 403 e nada gravado (é o que o XHR cru fazia).
        $semCsrf = $this->get('/modelo/modelo.pergunta.inclui.php', $jar, [
            'idModelo' => $idM, 'idPergunta' => $idP,
        ]);
        $this->assertSame(403, $semCsrf['code'], 'Endpoint de escrita deve exigir CSRF.');
        $this->assertSame('0', self::db("SELECT COUNT(*) FROM modelopergunta WHERE idModelo={$idM} AND idPergunta={$idP}"));
    }

    public function testIncluirPerguntaNoModeloComCsrfGrava(): void
    {
        $idM = $this->seedModelo('ok-modelo');
        $idP = $this->seedPergunta('ok-perg');

        $jar = $this->loggedInJar();
        $r = $this->get('/modelo/modelo.pergunta.inclui.php', $jar, [
            'idModelo' => $idM, 'idPergunta' => $idP, 'csrf' => $this->csrfToken($jar),
        ]);
        $this->assertSame(200, $r['code']);
        $this->assertStringContainsString('Incluido com sucesso', $r['body']);
        $this->assertSame('1', self::db("SELECT COUNT(*) FROM modelopergunta WHERE idModelo={$idM} AND idPergunta={$idP}"),
            'Com CSRF válido, a pergunta deve ser associada ao modelo.');
    }

    public function testRemoverPerguntaDoModeloComCsrfApaga(): void
    {
        $idM = $this->seedModelo('del-modelo');
        $idP = $this->seedPergunta('del-perg');
        self::db("INSERT INTO modelopergunta (idModelo, idPergunta, ordem) VALUES ({$idM}, {$idP}, 1)");

        $jar = $this->loggedInJar();
        $r = $this->get('/modelo/modelo.pergunta.apaga.php', $jar, [
            'idModelo' => $idM, 'idPergunta' => $idP, 'csrf' => $this->csrfToken($jar),
        ]);
        $this->assertSame(200, $r['code']);
        $this->assertSame('0', self::db("SELECT COUNT(*) FROM modelopergunta WHERE idModelo={$idM} AND idPergunta={$idP}"));
    }

    public function testIncluirRespostaNaPerguntaComCsrfGrava(): void
    {
        $idP = $this->seedPergunta('resp-perg', 'N', 'S');
        $desc = 'Alt' . self::MARKER;

        $jar = $this->loggedInJar();
        $r = $this->get('/pergunta/pergunta.resposta.inclui.php', $jar, [
            'idPergunta' => $idP, 'descricao' => $desc, 'csrf' => $this->csrfToken($jar),
        ]);
        $this->assertSame(200, $r['code']);
        $this->assertSame('1', self::db("SELECT COUNT(*) FROM resposta WHERE idPergunta={$idP} AND descricao='{$desc}'"),
            'Com CSRF válido, a resposta deve ser gravada na pergunta.');
    }
}
