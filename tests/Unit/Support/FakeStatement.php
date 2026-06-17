<?php

namespace Tests\Unit\Support;

/**
 * Double de PDOStatement: expõe os métodos que os controllers usam
 * (fetchAll / fetch / fetchColumn) sobre um result-set fixo.
 */
class FakeStatement
{
    public function __construct(private array $rows) {}

    public function fetchAll(): array
    {
        return $this->rows;
    }

    public function fetch()
    {
        return $this->rows[0] ?? false;
    }

    public function fetchColumn()
    {
        $first = $this->rows[0] ?? null;
        if (is_array($first)) {
            return array_values($first)[0] ?? 0;
        }
        return $first ?? 0;
    }
}
