<?php
declare(strict_types=1);

namespace Phlib\SchemaChange;

class Formatter
{
    public function tableIdentifier(string $tableIdentifier): string
    {
        return $this->quoteIdentifier($tableIdentifier); // @todo prefixing
    }

    public function quoteIdentifier(string $identifier): string
    {
        return $identifier; // @todo DB quoting
    }

    public function quoteValue(string $value): string
    {
        return $value; // @todo DB quoting
    }
}
