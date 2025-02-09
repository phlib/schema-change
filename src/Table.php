<?php

declare(strict_types=1);

namespace Phlib\SchemaChange;

abstract class Table implements Change
{
    use FormatterTrait;

    public function __construct(
        Formatter $formatter,
        protected readonly string $table,
    ) {
        $this->formatter = $formatter;
    }
}
