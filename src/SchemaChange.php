<?php
declare(strict_types=1);

namespace Phlib\SchemaChange;

use Phlib\Db\Adapter;
use Phlib\Db\Exception\Exception as DbException;
use Phlib\SchemaChange\Exception\RuntimeException;
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

    /**
     * @var OnlineChangeRunner
     */
    private $onlineChangeRunner;

    public function __construct(Adapter $db, OnlineChangeRunner $onlineChangeRunner = null)
    {
        $this->db = $db;
        $this->formatter = new Formatter($this->db);
        $this->onlineChangeRunner = $onlineChangeRunner;
    }

    public function mapNames(NameMapper $nameMapper): void
    {
        $this->formatter->setNameMapper($nameMapper);
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
        if (isset($this->onlineChangeRunner) &&
            $change instanceof OnlineChange && $change->getOnlineChange()
        ) {
            $this->onlineChangeRunner->execute($this->db->getConfig(), $change);
        } else {
            try {
                $this->db->execute($change->toSql());
            } catch (DbException $e) {
                throw RuntimeException::fromDbException($e);
            }
        }
    }
}
