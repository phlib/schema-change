<?php

declare(strict_types=1);

namespace Phlib\SchemaChange\Tests\Table;

use Phlib\SchemaChange\Formatter;
use Phlib\SchemaChange\Formatter\TestFake as FakeFormatter;
use Phlib\SchemaChange\Table\Alter;
use PHPUnit\Framework\TestCase;

class AlterTest extends TestCase
{
    private Formatter $formatter;

    public function setUp(): void
    {
        $this->formatter = new FakeFormatter();
        parent::setUp();
    }

    public function testRenameTable(): void
    {
        $table = new Alter($this->formatter, 'old_table_name');
        $table->rename('new_table_name');

        $expected = <<<SQL
ALTER TABLE `old_table_name`
RENAME TO `new_table_name`
SQL;
        static::assertEquals($expected, $table->toSql());
    }

    public function testDropColumnDefault(): void
    {
        $table = new Alter($this->formatter, 'table_name');
        $table->dropColumnDefault('column_name');

        $expected = <<<SQL
ALTER TABLE `table_name`
ALTER COLUMN `column_name` DROP DEFAULT
SQL;
        static::assertEquals($expected, $table->toSql());
    }

    public function testAddColumn(): void
    {
        $table = new Alter($this->formatter, 'table_name');
        $table->addColumn('column_name', 'INT(11)');

        $expected = <<<SQL
ALTER TABLE `table_name`
ADD `column_name` INT(11) NULL
SQL;
        static::assertEquals($expected, $table->toSql());
    }

    public function testChangeColumn(): void
    {
        $table = new Alter($this->formatter, 'table_name');
        $table->changeColumn('column_name', 'INT(11)')
            ->rename('new_name')
            ->notNull()
            ->autoIncrement();

        $expected = <<<SQL
ALTER TABLE `table_name`
CHANGE COLUMN `column_name` `new_name` INT(11) NOT NULL AUTO_INCREMENT
SQL;
        static::assertEquals($expected, $table->toSql());
    }

    public function testRemoveColumn(): void
    {
        $table = new Alter($this->formatter, 'table_name');
        $table->removeColumn('column_name');

        $expected = <<<SQL
ALTER TABLE `table_name`
DROP `column_name`
SQL;
        static::assertEquals($expected, $table->toSql());
    }

    public function testRemovePrimary(): void
    {
        $table = new Alter($this->formatter, 'table_name');
        $table->removePrimary();

        $expected = <<<SQL
ALTER TABLE `table_name`
DROP PRIMARY KEY
SQL;
        static::assertEquals($expected, $table->toSql());
    }

    public function testAddPrimary(): void
    {
        $table = new Alter($this->formatter, 'table_name');
        $table->addPrimary('column_1', 'column_2');

        $expected = <<<SQL
ALTER TABLE `table_name`
ADD PRIMARY KEY (`column_1`,`column_2`)
SQL;
        static::assertEquals($expected, $table->toSql());
    }

    public function testPrimary(): void
    {
        $table = new Alter($this->formatter, 'table_name');
        $table->primary('column_1', 'column_2');

        $expected = <<<SQL
ALTER TABLE `table_name`
DROP PRIMARY KEY,
ADD PRIMARY KEY (`column_1`,`column_2`)
SQL;
        static::assertEquals($expected, $table->toSql());
    }

    public function testAddIndex(): void
    {
        $table = new Alter($this->formatter, 'table_name');
        $table->addIndex('column_1', 'column_2')
            ->name('my_idx')
            ->unique();

        $expected = <<<SQL
ALTER TABLE `table_name`
ADD UNIQUE KEY `my_idx` (`column_1`, `column_2`)
SQL;
        static::assertEquals($expected, $table->toSql());
    }

    public function testRemoveIndex(): void
    {
        $table = new Alter($this->formatter, 'table_name');
        $table->removeIndex('my_idx');

        $expected = <<<SQL
ALTER TABLE `table_name`
DROP KEY `my_idx`
SQL;
        static::assertEquals($expected, $table->toSql());
    }

    public function testAttributes(): void
    {
        $table = new Alter($this->formatter, 'table_name');
        $table->attribute('DEFAULT CHARSET', 'ascii');

        $expected = <<<SQL
ALTER TABLE `table_name`
DEFAULT CHARSET=ascii
SQL;
        static::assertEquals($expected, $table->toSql());
    }

    public function testForce(): void
    {
        $table = new Alter($this->formatter, 'table_name');
        $table->force();

        $expected = <<<SQL
ALTER TABLE `table_name`
FORCE
SQL;
        static::assertEquals($expected, $table->toSql());
    }

    public function testComplex(): void
    {
        $table = new Alter($this->formatter, 'table_name');
        $table->rename('new_name');
        $table->dropColumnDefault('default_column');
        $table->addColumn('new_column', 'VARCHAR(255)')->after('column_1')->notNull()->defaultTo('default');
        $table->changeColumn('change_column', 'INT(11)')->rename('renamed')->notNull();
        $table->removeColumn('remove_column');
        $table->primary('column_1', 'column_2');

        $expected = <<<SQL
ALTER TABLE `table_name`
RENAME TO `new_name`,
ADD `new_column` VARCHAR(255) NOT NULL DEFAULT 'default' AFTER `column_1`,
ALTER COLUMN `default_column` DROP DEFAULT,
CHANGE COLUMN `change_column` `renamed` INT(11) NOT NULL,
DROP `remove_column`,
DROP PRIMARY KEY,
ADD PRIMARY KEY (`column_1`,`column_2`)
SQL;
        static::assertEquals($expected, $table->toSql());
    }
}
