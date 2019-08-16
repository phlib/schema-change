<?php
declare(strict_types=1);

namespace Phlib\SchemaChange\Table;

use Phlib\SchemaChange\Column;

class Alter extends Create
{
    /**
     * @var string
     */
    private $newName;

    /**
     * @var string[]
     */
    private $dropDefaultColumns = [];

    /**
     * @var string[]
     */
    private $changeColumns = [];

    /**
     * @var string[]
     */
    private $removeColumns = [];

    /**
     * @var string[]
     */
    private $removeIndexes = [];

    /**
     * @var bool
     */
    private $force = false;

    public function rename(string $newName): self
    {
        $this->newName = $newName;
        return $this;
    }

    public function dropColumnDefault(string $columnName): self
    {
        $this->dropDefaultColumns[] = $columnName;
        return $this;
    }

    /**
     * Change a column definition
     *
     * The full column definition must be supplied
     *
     * @param string $columnName
     * @param string $type
     * @return Column
     */
    public function changeColumn(string $columnName, string $type): Column
    {
        $column = new Column($this->formatter, $columnName, $type);
        $this->changeColumns[] = $column;
        return $column;
    }

    public function removeColumn(string $columnName): self
    {
        $this->removeColumns[] = $columnName;
        return $this;
    }

    public function removePrimary(): self
    {
        $this->primary = null;
        $this->primaryRemoveOld = true;
        return $this;
    }

    public function removeIndex(string $index): self
    {
        $this->removeIndexes[] = $index;
        return $this;
    }

    public function force(): self
    {
        $this->force = true;
        return $this;
    }

    public function toSql(): string
    {
        $tableName = $this->tableIdentifier($this->table);
        $ddl = "ALTER TABLE {$tableName}\n";

        $ddl .= $this->getCmds();

        return $ddl;
    }

    private function getCmds(): string
    {
        $cmds = [];

        // rename the table
        if (isset($this->newName)) {
            $cmds[] = 'RENAME TO ' . $this->tableIdentifier($this->newName);
        }
        // add columns
        foreach ($this->addColumns as $column) {
            $cmds[] = 'ADD ' . (string)$column;
        }
        // drop column defaults
        foreach ($this->dropDefaultColumns as $columnName) {
            $columnName = $this->quoteIdentifier($columnName);
            $cmds[] = "ALTER COLUMN {$columnName} DROP DEFAULT";
        }
        // change columns
        foreach ($this->changeColumns as $column) {
            $columnName = $this->quoteIdentifier($column->getName());
            $cmds[] = 'CHANGE COLUMN ' . $columnName . ' ' . (string)$column;
        }
        // remove fields
        foreach ($this->removeColumns as $column) {
            $cmds[] = 'DROP ' . $this->quoteIdentifier($column);
        }
        // primary key
        if ($this->primaryRemoveOld) {
            $cmds[] = 'DROP PRIMARY KEY';
        }
        if (isset($this->primary)) {
            $primary = implode(',', $this->quoteIdentifiers(...$this->primary));
            $cmds[] = "ADD PRIMARY KEY ($primary)";
        }
        // add indexes (key)
        foreach ($this->addIndexes as $index) {
            $cmds[] = 'ADD ' . (string)$index;
        }
        // remove indexes (key)
        foreach ($this->removeIndexes as $index) {
            $cmds[] = 'DROP KEY ' . $this->quoteIdentifier($index);
        }
        // attributes
        foreach ($this->attributes as $attribute => $value) {
            $cmds[] = $attribute . '=' . $value;
        }
        if ($this->force) {
            $cmds[] = 'FORCE';
        }

        return implode(",\n", $cmds);
    }
}