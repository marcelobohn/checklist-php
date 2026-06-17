<?php

namespace Tests;

use PHPUnit\Framework\TestCase;

/**
 * Base dos testes funcionais: cliente HTTP (curl) com cookie jar por teste,
 * helpers de login/CSRF e acesso ao banco via `docker compose`.
 *
 * Os testes exercitam o app REAL (dockerizado), de ponta a ponta.
 */
abstract class FunctionalTestCase extends TestCase
{
    /** Marcador embutido nos dados criados em teste, para limpeza automática. */
    protected const MARKER = '~TEST~';

    protected string $baseUrl;
    private array $jars = [];

    protected function setUp(): void
    {
        $this->baseUrl = rtrim(getenv('BASE_URL') ?: 'http://localhost/checklist', '/');
        self::exigeAppNoAr($this->baseUrl);
    }

    /** Cache do check de disponibilidade (verifica uma vez por execução). */
    private static ?bool $appNoAr = null;

    /** Pula o teste (com mensagem clara) se o app dockerizado não estiver acessível. */
    private static function exigeAppNoAr(string $baseUrl): void
    {
        if (self::$appNoAr === null) {
            $ch = curl_init($baseUrl . '/');
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_NOBODY         => true,
                CURLOPT_TIMEOUT        => 5,
            ]);
            curl_exec($ch);
            self::$appNoAr = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE) !== 0;
            curl_close($ch);
        }

        if (!self::$appNoAr) {
            self::markTestSkipped(
                "App não acessível em {$baseUrl}. Suba o ambiente: docker compose up -d --build"
            );
        }
    }

    protected function tearDown(): void
    {
        foreach ($this->jars as $jar) {
            @unlink($jar);
        }
        $this->jars = [];

        // Remove qualquer dado criado durante os testes (identificado pelo marcador).
        // Ordem importa por causa das FKs: registro (RESTRICT->modelo) primeiro;
        // ao apagá-lo o registroitem some em cascata, liberando as FKs RESTRICT
        // que referenciam pergunta/modelo. Apagar pergunta/modelo então remove
        // resposta e modelopergunta em cascata.
        $m = self::MARKER;
        self::db("DELETE FROM registro WHERE usuario LIKE '%{$m}%'");
        self::db("DELETE FROM resposta WHERE descricao LIKE '%{$m}%'");
        self::db("DELETE FROM pergunta WHERE descricao LIKE '%{$m}%'");
        self::db("DELETE FROM usuario  WHERE nome LIKE '%{$m}%'");
        self::db("DELETE FROM modelo   WHERE nome LIKE '%{$m}%'");
    }

    /**
     * Cria uma pergunta via o endpoint real (com CSRF) e retorna a resposta HTTP.
     * Usa idPergunta VAZIO de propósito — é o que o formulário envia para um
     * registro novo (ver regressão da #15).
     */
    protected function criaPergunta(string $jar, string $descricao, string $marcar = 'S', string $resposta = 'N'): array
    {
        return $this->get('/pergunta/pergunta.model.php', $jar, [
            'acao' => 'grava', 'idPergunta' => '', 'descricao' => $descricao,
            'marcar' => $marcar, 'resposta' => $resposta, 'csrf' => $this->csrfToken($jar),
        ]);
    }

    /** Cria um usuário via o endpoint real (com CSRF) e retorna a resposta HTTP. */
    protected function criaUsuario(string $jar, string $nome, string $senha, string $admin = 'N'): array
    {
        return $this->get('/usuario/usuario.model.php', $jar, [
            'acao' => 'grava', 'idUsuario' => '', 'nome' => $nome,
            'senha' => $senha, 'admin' => $admin, 'csrf' => $this->csrfToken($jar),
        ]);
    }

    /** Cria um cookie jar isolado (uma "sessão de navegador"). */
    protected function newJar(): string
    {
        $jar = tempnam(sys_get_temp_dir(), 'cklist_jar_');
        $this->jars[] = $jar;
        return $jar;
    }

    /**
     * Faz uma requisição HTTP.
     *
     * @param array{query?:array,post?:array,referer?:string} $opts
     * @return array{code:int, body:string}
     */
    protected function request(string $method, string $path, string $jar, array $opts = []): array
    {
        $url = $this->baseUrl . $path;
        if (!empty($opts['query'])) {
            $url .= (str_contains($url, '?') ? '&' : '?') . http_build_query($opts['query']);
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => false,   // queremos checar 302/403 diretamente
            CURLOPT_COOKIEJAR      => $jar,
            CURLOPT_COOKIEFILE     => $jar,
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_CUSTOMREQUEST  => $method,
        ]);
        if (!empty($opts['referer'])) {
            curl_setopt($ch, CURLOPT_REFERER, $opts['referer']);
        }
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($opts['post'] ?? []));
        }

        $body = curl_exec($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return ['code' => $code, 'body' => (string) $body];
    }

    protected function get(string $path, string $jar, array $query = []): array
    {
        return $this->request('GET', $path, $jar, [
            'query'   => $query,
            'referer' => $this->baseUrl . '/',
        ]);
    }

    protected function post(string $path, string $jar, array $post = [], array $query = []): array
    {
        return $this->request('POST', $path, $jar, [
            'post'    => $post,
            'query'   => $query,
            'referer' => $this->baseUrl . '/',
        ]);
    }

    /** Autentica como admin/admin no jar informado. */
    protected function login(string $jar, string $user = 'admin', string $pass = 'admin'): array
    {
        return $this->post('/login.php', $jar, ['usuario' => $user, 'senha' => $pass]);
    }

    /** Cria um jar já autenticado. */
    protected function loggedInJar(): string
    {
        $jar = $this->newJar();
        $r = $this->login($jar);
        $this->assertSame(302, $r['code'], 'Login admin/admin deveria redirecionar (302).');
        return $jar;
    }

    /** Extrai o token CSRF exposto na página de um módulo logado. */
    protected function csrfToken(string $jar): string
    {
        $r = $this->get('/pergunta/', $jar);
        if (preg_match('/CSRF_TOKEN\s*=\s*"([a-f0-9]+)"/', $r['body'], $m)) {
            return $m[1];
        }
        $this->fail('Não foi possível obter o token CSRF da página.');
    }

    /** Executa SQL no MySQL dockerizado e retorna a saída (sem cabeçalho). */
    protected static function db(string $sql): string
    {
        $pass = getenv('DB_PASSWORD') ?: '123';
        $cmd = sprintf(
            'docker compose exec -T db mysql -uroot -p%s -N checklist -e %s 2>/dev/null',
            escapeshellarg($pass),
            escapeshellarg($sql)
        );
        return trim((string) shell_exec($cmd));
    }
}
