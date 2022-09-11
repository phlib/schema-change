<?php
declare(strict_types=1);

namespace Phlib\SchemaChange\Table;

use Phlib\SchemaChange\Column;
use Phlib\SchemaChange\Index;
use Phlib\SchemaChange\Table;

class Create extends Table
{
    /**
     * @var Column[]
     */
    protected $addColumns = [];

    /**
     * @var string[]
     */
    protected $primary;

    /**
     * @var bool
     */
    protected $primaryRemoveOld;

    /**
     * @var Index[]
     */
    protected $addIndexes = [];

    /**
     * @var array
     */
    protected $attributes = [];

    public function addColumn(string $name, string $type): Column
    {
        $column = new Column($this->formatter, $name, $type);
        $this->addColumns[] = $column;
        return $column;
    }

    public function addIndex(string ...$columns): Index
    {
        $index = new Index($this->formatter, $this->table, ...$columns);
        $this->addIndexes[] = $index;
        return $index;
    }

    public function addPrimary(string ...$columns): self
    {
        $this->primary = $columns;
        $this->primaryRemoveOld = false;
        return $this;
    }

    public function primary(string ...$columns): self
    {
        $this->primary = $columns;
        $this->primaryRemoveOld = true;
        return $this;
    }

    public function attribute(string $name, string $value): self
    {
        $this->attributes[$name] = $value;
        return $this;
    }

    public function toSql(): string
    {
        $tableName = $this->tableIdentifier($this->table);
        $ddl = "CREATE TABLE {$tableName} (\n";

        $rows = [];
        foreach ($this->addColumns as $column) {
            $rows[] = (string)$column;
        }

        // primary key
        if (isset($this->primary)) {
            $primary = implode(',', $this->quoteIdentifiers(...$this->primary));
            $rows[] = "PRIMARY KEY ($primary)";
        }

        // indexes (key)
        foreach ($this->addIndexes as $index) {
            $rows[] = (string)$index;
        }

        $ddl .= implode(",\n", $rows) . "\n)";

        // table attributes
        if (count($this->attributes) > 0) {
            $tableAttributes = [];
            foreach ($this->attributes as $attribute => $value) {
                $tableAttributes[] = $attribute . '=' . $value;
            }
            $ddl .= ' ' . implode(' ', $tableAttributes);
        }

        return $ddl;
    }
}
