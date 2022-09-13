<?php

declare(strict_types=1);

namespace Phlib\SchemaChange\Tests\Table;

use Phlib\SchemaChange\Formatter;
use Phlib\SchemaChange\Formatter\TestFake as FakeFormatter;
use Phlib\SchemaChange\Table\Drop;
use PHPUnit\Framework\TestCase;

class DropTest extends TestCase
{
    /**
     * @var Formatter
     */
    private $formatter;

    public function setUp(): void
    {
        $this->formatter = new FakeFormatter();
        parent::setUp();
    }

    public function testDrop(): void
    {
        $table = new Drop($this->formatter, 'table_name');

        $expected = <<<SQL
DROP TABLE `table_name`
SQL;
        static::assertEquals($expected, $table->toSql());
    }
}
