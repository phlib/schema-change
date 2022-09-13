<?php

declare(strict_types=1);

namespace Phlib\SchemaChange\Tests;

use Phlib\SchemaChange\Formatter;
use Phlib\SchemaChange\Formatter\TestFake as FakeFormatter;
use Phlib\SchemaChange\Index;
use PHPUnit\Framework\TestCase;

class IndexTest extends TestCase
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

    public function testIndexWithGeneratedName(): void
    {
        $index = new Index($this->formatter, 'table_name', 'column_1');
        static::assertEquals('KEY `table_name_column_1_idx` (`column_1`)', (string)$index);
    }

    public function testComplexIndexWithGeneratedName(): void
    {
        $index = new Index($this->formatter, 'table_name', 'column_1', 'column_2');
        static::assertEquals('KEY `table_name_column_1_column_2_idx` (`column_1`, `column_2`)', (string)$index);
    }

    public function testNamedIndex(): void
    {
        $index = (new Index($this->formatter, 'table_name', 'column_1'))
            ->name('my_idx');
        static::assertEquals('KEY `my_idx` (`column_1`)', (string)$index);
    }

    public function testUniqueIndex(): void
    {
        $index = (new Index($this->formatter, 'table_name', 'column_1'))
            ->name('my_idx')
            ->unique();
        static::assertEquals('UNIQUE KEY `my_idx` (`column_1`)', (string)$index);
    }
}
