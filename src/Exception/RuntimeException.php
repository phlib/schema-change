<?php
declare(strict_types=1);

namespace Phlib\SchemaChange\Exception;

use Phlib\Db\Exception\Exception as DbException;
use Phlib\Db\Exception\InvalidQueryException;
use Phlib\Db\Exception\RuntimeException as DbRuntimeException;
use Symfony\Component\Process\Exception\ProcessFailedException;

class RuntimeException extends \RuntimeException implements Exception
{
    public static function fromDbException(DbException $e)
    {
        if ($e instanceof DbRuntimeException || $e instanceof InvalidQueryException) {
            $newSelf = new static($e->getMessage(), 0, $e);
            $newSelf->code = $e->getCode();
            return $newSelf;
        }
        return new static('Unknown DB exception', 0, $e);
    }

    public static function fromProcessFailException(ProcessFailedException $e)
    {
        return new static($e->getMessage(), $e->getCode(), $e);
    }
}
