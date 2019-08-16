<?php
declare(strict_types=1);

namespace Phlib\SchemaChange;

class Index
{
    use FormatterTrait;

    /**
     * @var string
     */
    private $tableName;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string[]
     */
    private $columns;

    /**
     * @var bool
     */
    private $unique;

    public function __construct(
        Formatter $formatter,
        string $tableName,
        string ...$columns
    ) {
        $this->formatter = $formatter;
        $this->tableName = $tableName;
        $this->columns = $columns;
    }

    public function name(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function unique(bool $unique = true): self
    {
        $this->unique = $unique;
        return $this;
    }

    public function toSql(): string
    {
        $prepend = '';
        if ($this->unique) {
            $prepend = 'UNIQUE ';
        }

        $name = $this->quoteIdentifier($this->name ?? $this->generateName());
        $fields = implode(', ', $this->quoteIdentifiers(...$this->columns));

        return $prepend . "KEY $name ($fields)";
    }

    public function __toString(): string
    {
        return $this->toSql();
    }

    private function generateName(): string
    {
        $parts = array_merge([$this->tableName], $this->columns, ['idx']);
        return implode('_', $parts);
    }
}
