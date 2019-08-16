<?php
declare(strict_types=1);

namespace Phlib\SchemaChange;

interface Change
{
    public function toSql(): string;
}
