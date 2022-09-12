<?php

declare(strict_types=1);

namespace Phlib\SchemaChange\Formatter;

use Phlib\SchemaChange\Formatter;
use Phlib\SchemaChange\NameMapper;

class TestFake implements Formatter
{
    public function setNameMapper(NameMapper $nameMapper): void
    {
        return;
    }

    public function tableIdentifier(string $tableIdentifier): string
    {
        return "`{$tableIdentifier}`";
    }

    public function quoteIdentifier(string $identifier): string
    {
        return "`{$identifier}`";
    }

    public function quoteValue(string $value): string
    {
        return "'{$value}'";
    }
}
