<?php
declare(strict_types=1);

namespace Phlib\SchemaChange;

interface Formatter
{
    public function setNameMapper(NameMapper $nameMapper): void;

    public function tableIdentifier(string $tableIdentifier): string;

    public function quoteIdentifier(string $identifier): string;

    public function quoteValue(string $value): string;
}
