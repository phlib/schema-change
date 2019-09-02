<?php
declare(strict_types=1);

namespace Phlib\SchemaChange\Tests\Table;

use Phlib\SchemaChange\Formatter;
use Phlib\SchemaChange\Formatter\TestFake as FakeFormatter;
use Phlib\SchemaChange\Table\Create;
use PHPUnit\Framework\TestCase;

class CreateTest extends TestCase
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

    public function testAddColumn()
    {
        $table = new Create($this->formatter, 'table_name');
        $table->addColumn('column_name', 'INT(11)');

        $expected = <<<SQL
CREATE TABLE `table_name` (
`column_name` INT(11) NULL
)
SQL;
        static::assertEquals($expected, $table->toSql());
    }

    public function testAddPrimary()
    {
        $table = new Create($this->formatter, 'table_name');
        $table->addPrimary('column_1', 'column_2');

        $expected = <<<SQL
CREATE TABLE `table_name` (
PRIMARY KEY (`column_1`,`column_2`)
)
SQL;
        static::assertEquals($expected, $table->toSql());
    }

    public function testPrimary()
    {
        $table = new Create($this->formatter, 'table_name');
        $table->primary('column_1', 'column_2');

        $expected = <<<SQL
CREATE TABLE `table_name` (
PRIMARY KEY (`column_1`,`column_2`)
)
SQL;
        static::assertEquals($expected, $table->toSql());
    }

    public function testAddIndex()
    {
        $table = new Create($this->formatter, 'table_name');
        $table->addIndex('column_1', 'column_2')
            ->name('my_idx')
            ->unique();

        $expected = <<<SQL
CREATE TABLE `table_name` (
UNIQUE KEY `my_idx` (`column_1`, `column_2`)
)
SQL;
        static::assertEquals($expected, $table->toSql());
    }

    public function testAttributes()
    {
        $table = new Create($this->formatter, 'table_name');
        $table->addColumn('column_1', 'INT(11)')->notNull();
        $table->attribute('DEFAULT CHARSET', 'ascii');

        $expected = <<<SQL
CREATE TABLE `table_name` (
`column_1` INT(11) NOT NULL
) DEFAULT CHARSET=ascii
SQL;
        static::assertEquals($expected, $table->toSql());
    }

    public function testComplex()
    {
        $table = new Create($this->formatter, 'table_name');
        $table->addColumn('column_1', 'INT(11)')->notNull()->autoIncrement();
        $table->addColumn('column_2', 'TIMESTAMP')->notNull()->defaultRaw('CURRENT_TIMESTAMP');
        $table->primary('column_1');
        $table->attribute('DEFAULT CHARSET', 'ascii');

        $expected = <<<SQL
CREATE TABLE `table_name` (
`column_1` INT(11) NOT NULL AUTO_INCREMENT,
`column_2` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
PRIMARY KEY (`column_1`)
) DEFAULT CHARSET=ascii
SQL;
        static::assertEquals($expected, $table->toSql());
    }
}
