<?php

declare(strict_types=1);

namespace Phlib\SchemaChange\Table;

use Phlib\SchemaChange\Table;

class Drop extends Table
{
    public function toSql(): string
    {
        $tableName = $this->tableIdentifier($this->table);
        return "DROP TABLE {$tableName}";
    }
}
