<?php

namespace Tests\Unit\Support;

/**
 * Double de App\ConexaoBD para testes unitários (duck typing — os controllers
 * só chamam ->query()). Registra as queries executadas e devolve result-sets
 * pré-definidos, na ordem.
 *
 * Cada result-set é um array de linhas; cada linha um array (estilo FETCH_BOTH).
 * Para count(*), passe um result-set como [<n>] (uma "linha" escalar).
 */
class FakeConexao
{
    /** @var array<int,array{sql:string,params:array}> */
    public array $queries = [];

    /** @var array<int,array> fila de result-sets */
    private array $results;

    public function __construct(array $results = [])
    {
        $this->results = $results;
    }

    public function query($sql, $params = array())
    {
        $this->queries[] = ['sql' => $sql, 'params' => $params];
        $rows = array_shift($this->results);
        return new FakeStatement($rows ?? []);
    }

    /** Última query registrada (conveniência para asserções). */
    public function ultimaQuery(): array
    {
        return $this->queries[count($this->queries) - 1] ?? ['sql' => '', 'params' => []];
    }
}
