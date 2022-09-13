<?php

declare(strict_types=1);

namespace Phlib\SchemaChange\Tests;

use Phlib\SchemaChange\Column;
use Phlib\SchemaChange\Formatter;
use Phlib\SchemaChange\Formatter\TestFake as FakeFormatter;
use PHPUnit\Framework\TestCase;

class ColumnTest extends TestCase
{
    private Formatter $formatter;

    public function setUp(): void
    {
        $this->formatter = new FakeFormatter();
        parent::setUp();
    }

    public function testBasicColumn(): void
    {
        $column = new Column($this->formatter, 'int_column', 'INT(11)');
        static::assertEquals('`int_column` INT(11) NULL', (string)$column);
    }

    public function testNullableColumn(): void
    {
        $column = (new Column($this->formatter, 'nullable_column', 'VARCHAR(255)'))
            ->nullable();
        static::assertEquals('`nullable_column` VARCHAR(255) NULL', (string)$column);
    }

    public function testNotNullColumn(): void
    {
        $column = (new Column($this->formatter, 'nullable_column', 'VARCHAR(255)'))
            ->notNull();
        static::assertEquals('`nullable_column` VARCHAR(255) NOT NULL', (string)$column);
    }

    public function testRenameColumn(): void
    {
        $column = (new Column($this->formatter, 'old_column_name', 'VARCHAR(255)'))
            ->rename('new_column_name');
        static::assertEquals('`new_column_name` VARCHAR(255) NULL', (string)$column);
    }

    public function testUnsignedColumn(): void
    {
        $column = (new Column($this->formatter, 'unsigned_column', 'TINYINT(1)'))
            ->unsigned();
        static::assertEquals('`unsigned_column` TINYINT(1) UNSIGNED NULL', (string)$column);
    }

    public function testAsciiEncoding(): void
    {
        $column = (new Column($this->formatter, 'ascii_column', 'TEXT'))
            ->encoding('ascii');
        static::assertEquals('`ascii_column` TEXT CHARACTER SET ascii NULL', (string)$column);
    }

    public function testAsciiEncodingCollate(): void
    {
        $column = (new Column($this->formatter, 'ascii_column', 'TEXT'))
            ->encoding('ascii', 'ascii_general_ci');
        static::assertEquals('`ascii_column` TEXT CHARACTER SET ascii COLLATE ascii_general_ci NULL', (string)$column);
    }

    public function testDefaultTo(): void
    {
        $column = (new Column($this->formatter, 'default_column', 'VARCHAR(255)'))
            ->notNull()
            ->defaultTo('something');
        static::assertEquals('`default_column` VARCHAR(255) NOT NULL DEFAULT \'something\'', (string)$column);
    }

    public function testDefaultRaw(): void
    {
        $column = (new Column($this->formatter, 'ts_column', 'TIMESTAMP'))
            ->notNull()
            ->defaultRaw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        static::assertEquals('`ts_column` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP', (string)$column);
    }

    public function testAutoIncrement(): void
    {
        $column = (new Column($this->formatter, 'auto_id', 'INT(11)'))
            ->notNull()
            ->autoIncrement();
        static::assertEquals('`auto_id` INT(11) NOT NULL AUTO_INCREMENT', (string)$column);
    }

    public function testAfterColumn(): void
    {
        $column = (new Column($this->formatter, 'after_column', 'INT(11)'))
            ->after('other_column');
        static::assertEquals('`after_column` INT(11) NULL AFTER `other_column`', (string)$column);
    }

    public function testFirstColumn(): void
    {
        $column = (new Column($this->formatter, 'first_column', 'INT(11)'))
            ->first();
        static::assertEquals('`first_column` INT(11) NULL FIRST', (string)$column);
    }
}
