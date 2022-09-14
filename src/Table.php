<?php

declare(strict_types=1);

namespace Phlib\SchemaChange;

abstract class Table implements Change
{
    use FormatterTrait;

    protected string $table;

    public function __construct(Formatter $formatter, string $table)
    {
        $this->formatter = $formatter;
        $this->table = $table;
    }
}
