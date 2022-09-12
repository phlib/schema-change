<?php
declare(strict_types=1);

namespace Phlib\SchemaChange\Tests\Formatter;

use Phlib\Db\Adapter;
use Phlib\SchemaChange\Formatter\Db;
use Phlib\SchemaChange\NameMapper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DbTest extends TestCase
{
    /**
     * @var Adapter|MockObject
     */
    private $db;

    /**
     * @var Adapter\QuoteHandler|MockObject
     */
    private $quoter;

    public function setUp(): void
    {
        $this->quoter = $this->createMock(Adapter\QuoteHandler::class);
        $this->db = $this->createMock(Adapter::class);

        $this->db->expects(static::any())
            ->method('quote')
            ->willReturn($this->quoter);
        parent::setUp();
    }

    public function testQuoteIdentifier(): void
    {
        $identifier = 'my_identifier';

        $this->quoter->expects(static::once())
            ->method('identifier')
            ->with($identifier)
            ->willReturn("`$identifier`");

        $formatter = new Db($this->db);
        static::assertEquals("`$identifier`", $formatter->quoteIdentifier($identifier));
    }

    public function testQuoteValue(): void
    {
        $value = 'my value';

        $this->quoter->expects(static::once())
            ->method('value')
            ->with($value)
            ->willReturn("'$value'");

        $formatter = new Db($this->db);
        static::assertEquals("'$value'", $formatter->quoteValue($value));
    }

    public function testTableIdentifier(): void
    {
        $table = 'my_table';

        $this->quoter->expects(static::once())
            ->method('identifier')
            ->with($table)
            ->willReturn("`$table`");

        $formatter = new Db($this->db);
        static::assertEquals("`$table`", $formatter->tableIdentifier($table));
    }

    public function testTableIdentifierWithMapping(): void
    {
        $nameMapper = new class implements NameMapper {
            public function mapTableName(string $table): string
            {
                return "my_{$table}_name";
            }
        };

        $table = 'table';

        $this->quoter->expects(static::once())
            ->method('identifier')
            ->with("my_table_name")
            ->willReturn("`my_table_name`");

        $formatter = new Db($this->db);
        $formatter->setNameMapper($nameMapper);
        static::assertEquals("`my_table_name`", $formatter->tableIdentifier($table));
    }
}
