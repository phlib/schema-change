<?php
declare(strict_types=1);

namespace Phlib\SchemaChange;

interface OnlineChange
{
    public function getName(): string;

    public function getOnlineChange(): bool;

    public function toOnlineAlter(): string;
}
