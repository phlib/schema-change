<?php
declare(strict_types=1);

namespace Phlib\SchemaChange;

use Phlib\SchemaChange\Table\Alter;
use Phlib\SchemaChange\Table\Create;
use Phlib\SchemaChange\Table\Drop;

class SchemaChange
{
    /**
     * @var Formatter
     */
    private $formatter;

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
        $sql = $change->toSql();
        echo $sql; // @todo SQL execution
    }
}
