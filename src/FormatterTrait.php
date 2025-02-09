<?php

declare(strict_types=1);

namespace Phlib\SchemaChange;

trait FormatterTrait
{
    protected readonly Formatter $formatter;

    protected function tableIdentifier(string $tableIdentifer): string
    {
        return $this->formatter->tableIdentifier($tableIdentifer);
    }

    protected function quoteIdentifier(string $identifier): string
    {
        return $this->formatter->quoteIdentifier($identifier);
    }

    protected function quoteIdentifiers(string ...$identifiers): array
    {
        return array_map([$this, 'quoteIdentifier'], $identifiers);
    }

    protected function quoteValue(string $value): string
    {
        return $this->formatter->quoteValue($value);
    }
}
