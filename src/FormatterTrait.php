<?php
declare(strict_types=1);

namespace Phlib\SchemaChange;

trait FormatterTrait
{
    /**
     * @var Formatter
     */
    protected $formatter;

    protected function tableIdentifier(string $tableIdentifer): string
    {
        return $this->formatter->tableIdentifier($tableIdentifer);
    }

    protected function quoteIdentifier(string $identifier): string
    {
        return $this->formatter->quoteIdentifier($identifier);
    }

    protected function quoteIdentifiers(string ...$identifiers)
    {
        return array_map([$this, 'quoteIdentifier'], $identifiers);
    }

    protected function quoteValue(string $value): string
    {
        return $this->formatter->quoteValue($value);
    }
}
