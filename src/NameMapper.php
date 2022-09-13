<?php

declare(strict_types=1);

namespace Phlib\SchemaChange;

interface NameMapper
{
    public function mapTableName(string $table): string;
}
