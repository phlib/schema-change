<?php
declare(strict_types=1);

namespace Phlib\SchemaChange\Tests;

use Phlib\Db\Adapter;
use Phlib\Db\Exception\InvalidQueryException;
use Phlib\SchemaChange\Change;
use Phlib\SchemaChange\Exception\RuntimeException;
use Phlib\SchemaChange\Formatter;
use Phlib\SchemaChange\NameMapper;
use Phlib\SchemaChange\OnlineChange;
use Phlib\SchemaChange\OnlineChangeRunner;
use Phlib\SchemaChange\SchemaChange;
use Phlib\SchemaChange\Table\Alter;
use Phlib\SchemaChange\Table\Create;
use Phlib\SchemaChange\Table\Drop;
use PHPUnit\Framework\TestCase;

class SchemaChangeTest extends TestCase
{
    public function testMapNames(): void
    {
        $db = $this->createMock(Adapter::class);
        $formatter = $this->createMock(Formatter::class);
        $nameMapper = $this->createMock(NameMapper::class);

        $formatter->expects(static::once())
            ->method('setNameMapper')
            ->with($nameMapper);

        $schemaChange = new SchemaChange($db, null, $formatter);
        $schemaChange->mapNames($nameMapper);
    }

    public function testCreate(): void
    {
        $db = $this->createMock(Adapter::class);
        $formatter = new Formatter\TestFake();

        $schemaChange = new SchemaChange($db, null, $formatter);
        $create = $schemaChange->create('table_name');

        static::assertInstanceOf(Create::class, $create);

        $create->addColumn('column_name', 'INT(11)');

        $expected = <<<SQL
CREATE TABLE `table_name` (
`column_name` INT(11) NULL
)
SQL;
        // basic smoke test to ensure that formatter and table name are passed in
        static::assertEquals($expected, $create->toSql());
    }

    public function testAlter(): void
    {
        $db = $this->createMock(Adapter::class);
        $formatter = new Formatter\TestFake();

        $schemaChange = new SchemaChange($db, null, $formatter);
        $alter = $schemaChange->alter('table_name');

        static::assertInstanceOf(Alter::class, $alter);

        $alter->addColumn('column_name', 'INT(11)');

        $expected = <<<SQL
ALTER TABLE `table_name`
ADD `column_name` INT(11) NULL
SQL;
        // basic smoke test to ensure that formatter and table name are passed in
        static::assertEquals($expected, $alter->toSql());
    }

    public function testDrop(): void
    {
        $db = $this->createMock(Adapter::class);
        $formatter = new Formatter\TestFake();

        $schemaChange = new SchemaChange($db, null, $formatter);
        $drop = $schemaChange->drop('table_name');

        static::assertInstanceOf(Drop::class, $drop);

        $expected = <<<SQL
DROP TABLE `table_name`
SQL;
        // basic smoke test to ensure that formatter and table name are passed in
        static::assertEquals($expected, $drop->toSql());
    }

    public function testExecuteChange(): void
    {
        $db = $this->createMock(Adapter::class);
        $formatter = new Formatter\TestFake();
        $change = $this->createMock(Change::class);

        $change->expects(static::once())
            ->method('toSql')
            ->willReturn('some sql');

        $db->expects(static::once())
            ->method('execute')
            ->with('some sql');

        $schemaChange = new SchemaChange($db, null, $formatter);
        $schemaChange->execute($change);
    }

    public function testExecuteChangeException(): void
    {
        static::expectException(RuntimeException::class);

        $db = $this->createMock(Adapter::class);
        $formatter = new Formatter\TestFake();
        $change = $this->createMock(Change::class);

        $change->expects(static::once())
            ->method('toSql')
            ->willReturn('some sql');

        $db->expects(static::once())
            ->method('execute')
            ->with('some sql')
            ->willThrowException(static::createMock(InvalidQueryException::class));

        $schemaChange = new SchemaChange($db, null, $formatter);
        $schemaChange->execute($change);
    }

    public function testExecuteOnlineChange(): void
    {
        $db = $this->createMock(Adapter::class);
        $onlineChangeRunner = $this->createMock(OnlineChangeRunner::class);
        $formatter = new Formatter\TestFake();
        $change = $this->getDummyOnlineChange();
        $dbConfig = ['db'=>'config'];

        $db->expects(static::once())
            ->method('getConfig')
            ->willReturn($dbConfig);

        $onlineChangeRunner->expects(static::once())
            ->method('execute')
            ->with($dbConfig, $change);

        $schemaChange = new SchemaChange($db, $onlineChangeRunner, $formatter);
        $schemaChange->execute($change);
    }

    private function getDummyOnlineChange(): Change
    {
        return new class implements OnlineChange, Change {
            public function getName(): string
            {
                return 'table_name';
            }

            public function getOnlineChange(): bool
            {
                return true;
            }

            public function toOnlineAlter(): string
            {
                return 'DO STUFF TO `table_name`';
            }

            public function toSql(): string
            {
                return 'DO STUFF TO `table_name`';
            }
        };
    }
}
