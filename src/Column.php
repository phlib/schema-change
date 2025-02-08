<?php

declare(strict_types=1);

namespace Phlib\SchemaChange;

use Phlib\Db\SqlFragment;

class Column implements \Stringable
{
    use FormatterTrait;

    private const POSITION_FIRST = 'FIRST';

    private const POSITION_AFTER = 'AFTER';

    private string $name;

    private string $type;

    private string $newName;

    private bool $unsigned;

    private string $encoding;

    private string $collate;

    private bool $nullable = true;

    private string|SqlFragment $default;

    private bool $auto;

    private array $position;

    public function __construct(Formatter $formatter, string $name, string $type)
    {
        $this->formatter = $formatter;
        $this->name = $name;
        $this->type = $type;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function rename(string $newName): self
    {
        $this->newName = $newName;
        return $this;
    }

    public function type(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function notNull(): self
    {
        $this->nullable = false;
        return $this;
    }

    public function nullable(): self
    {
        $this->nullable = true;
        return $this;
    }

    public function autoIncrement(): self
    {
        $this->auto = true;
        return $this;
    }

    public function unsigned(): self
    {
        $this->unsigned = true;
        return $this;
    }

    public function signed(): self
    {
        $this->unsigned = false;
        return $this;
    }

    public function first(): self
    {
        $this->position = [self::POSITION_FIRST, ''];
        return $this;
    }

    public function after(string $columnName): self
    {
        $this->position = [self::POSITION_AFTER, $columnName];
        return $this;
    }

    public function defaultTo(string $value): self
    {
        $this->default = $value;
        return $this;
    }

    public function defaultRaw(string $value): self
    {
        $this->default = new SqlFragment($value);
        return $this;
    }

    public function encoding(string $encoding, ?string $collate = null): self
    {
        $this->encoding = $encoding;
        if (isset($collate)) {
            $this->collate = $collate;
        }
        return $this;
    }

    public function toSql(): string
    {
        $columnName = $this->quoteIdentifier($this->name);
        $definition = [];

        if (isset($this->newName)) {
            $columnName = $this->quoteIdentifier($this->newName);
        }

        if (isset($this->unsigned) && $this->unsigned === true) {
            $definition[] = 'UNSIGNED';
        }
        if (isset($this->encoding)) {
            $definition[] = 'CHARACTER SET ' . $this->encoding;
            if (isset($this->collate)) {
                $definition[] = 'COLLATE ' . $this->collate;
            }
        }

        $definition[] = ($this->nullable === false) ? 'NOT NULL' : 'NULL';

        if (isset($this->default)) {
            $value = $this->default;
            if (!$value instanceof SqlFragment) {
                $value = $this->quoteValue($value);
            }
            $definition[] = 'DEFAULT ' . $value;
        }

        if (isset($this->auto) && $this->auto === true) {
            $definition[] = 'AUTO_INCREMENT';
        }

        if (isset($this->position)) {
            [$position, $value] = $this->position;
            $position = strtoupper($position);
            $value = trim((string)$value);
            if ($position === self::POSITION_FIRST) {
                $definition[] = $position;
            } elseif ($value !== '') {
                $definition[] = $position . ' ' . $this->quoteIdentifier($value);
            }
        }

        $definition = implode(' ', $definition);
        return trim($columnName . ' ' . $this->type . ' ' . $definition);
    }

    public function __toString(): string
    {
        return $this->toSql();
    }
}
