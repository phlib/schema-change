<?php
declare(strict_types=1);

namespace Phlib\SchemaChange;

use Phlib\Db\Adapter;
use Phlib\SchemaChange\Table\Alter;
use Phlib\SchemaChange\Table\Create;
use Phlib\SchemaChange\Table\Drop;

class SchemaChange
{
    /**
     * @var Adapter
     */
    private $db;

    /**
     * @var Formatter
     */
    private $formatter;

    public function __construct(Adapter $db)
    {
        $this->db = $db;
        $this->formatter = new Formatter($this->db);
    }

    public function create(string $table): Create
    {
        return new Create($this->formatter, $table);
    }

    public function alter(string $table): Alter
    {
        return new Alter($this->formatter, $table);
    }

    public function drop(string $table): Drop
    {
        return new Drop($this->formatter, $table);
    }

    public function execute(Change $change): void
    {
        $this->db->execute($change->toSql());
    }
}
