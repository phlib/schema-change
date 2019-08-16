<?php
declare(strict_types=1);

namespace Phlib\SchemaChange;

use Phlib\Db\Adapter;

class Formatter
{
    /**
     * @var Adapter
     */
    private $db;

    public function __construct(Adapter $db)
    {
        $this->db = $db;
    }

    public function tableIdentifier(string $tableIdentifier): string
    {
        return $this->quoteIdentifier($tableIdentifier); // @todo prefixing
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
