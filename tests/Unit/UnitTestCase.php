<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Base dos testes unitários.
 *
 * As classes de domínio conectam no banco no construtor (`new ConexaoBD()`).
 * Para testá-las em isolamento, instanciamos SEM construtor e injetamos um
 * double na propriedade pública `$bd` — sem tocar no código de produção.
 */
abstract class UnitTestCase extends TestCase
{
    protected function comBd(string $classe, object $bd): object
    {
        $obj = (new \ReflectionClass($classe))->newInstanceWithoutConstructor();
        $obj->bd = $bd;
        return $obj;
    }
}
