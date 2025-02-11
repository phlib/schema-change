<?php

declare(strict_types=1);

namespace Phlib\SchemaChange\Formatter;

use Phlib\Db\Adapter;
use Phlib\SchemaChange\Formatter;
use Phlib\SchemaChange\NameMapper;

class Db implements Formatter
{
    private NameMapper $nameMapper;

    public function __construct(
        private readonly Adapter $db,
    ) {
    }

    public function setNameMapper(NameMapper $nameMapper): void
    {
        $this->nameMapper = $nameMapper;
    }

    public function tableIdentifier(string $tableIdentifier): string
    {
        if (isset($this->nameMapper)) {
            $tableIdentifier = $this->nameMapper->mapTableName($tableIdentifier);
        }
        return $this->quoteIdentifier($tableIdentifier);
    }

    public function quoteIdentifier(string $identifier): string
    {
        return $this->db->quote()->identifier($identifier);
    }

    public function quoteValue(string $value): string
    {
        return $this->db->quote()->value($value);
    }
}
