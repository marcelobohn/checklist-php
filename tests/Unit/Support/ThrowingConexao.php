<?php

namespace Tests\Unit\Support;

/**
 * Double de App\ConexaoBD cujo query() sempre lança PDOException — para exercitar
 * os caminhos de tratamento de erro (ex.: apagar() devolvendo false).
 */
class ThrowingConexao
{
    public function query($sql, $params = array())
    {
        throw new \PDOException('falha simulada');
    }
}
