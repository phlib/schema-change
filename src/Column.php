<?php
declare(strict_types=1);

namespace Phlib\SchemaChange;

class Column
{
    use FormatterTrait;

    private const POSITION_FIRST = 'FIRST';
    private const POSITION_AFTER = 'AFTER';

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $newName;

    /**
     * @var bool
     */
    private $unsigned;

    /**
     * @var string
     */
    private $encoding;

    /**
     * @var string
     */
    private $collate;

    /**
     * @var bool
     */
    private $nullable;

    /**
     * @var array
     */
    private $default;

    /**
     * @var bool
     */
    private $auto;

    /**
     * @var array
     */
    private $position;

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

    /**
     * Set column default
     *
     * Pass `null` for value to remove column default
     *
     * @param string|null $value
     * @param bool $isConstant
     * @return self
     */
    public function defaultTo(?string $value, bool $isConstant = false): self
    {
        if ($value === null) {
            $this->default = null;
        } else {
            $this->default = [$value, $isConstant];
        }
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

        if ($this->unsigned === true) {
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
            [$value, $isConstant] = $this->default;
            if (!$isConstant) {
                $value = $this->quoteValue($value);
            }
            $definition[] = 'DEFAULT ' . $value;
        }

        if ($this->auto === true) {
            $definition[] = 'AUTO_INCREMENT';
        }

        if (isset($this->position)) {
            [$position, $value] = $this->position;
            $position = strtoupper($position);
            $value = trim((string)$value);
            if ($position == self::POSITION_FIRST) {
                $definition[] = $position;
            } elseif ($value != '') {
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