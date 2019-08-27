<?php
declare(strict_types=1);

namespace Phlib\SchemaChange\Tests;

use Phlib\SchemaChange\Exception\RuntimeException;
use Phlib\SchemaChange\OnlineChange;
use Phlib\SchemaChange\OnlineChangeRunner;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class OnlineChangeRunnerTest extends TestCase
{
    public function testExecute()
    {
        $process = $this->createMock(Process::class);
        $passedCmd = null;
        $processFactory = function ($cmd) use ($process, &$passedCmd) {
            $passedCmd = $cmd;
            return $process;
        };

        $onlineChange = $this->getDummyOnlineChange();

        $dbConfig = [
            'host' => '127.0.0.1',
            'port' => '33066',
            'dbname' => 'my_schema',
            'username' => 'user',
            'password' => 'pass',
        ];
        $expectedDsn = 'D=my_schema,t=table_name,h=127.0.0.1,u=user,p=pass,P=33066';

        $process->expects(static::once())
            ->method('mustRun');

        $onlineChangeRunner = new OnlineChangeRunner(['path_to_bin'], $processFactory);
        $onlineChangeRunner->execute($dbConfig, $onlineChange);

        static::assertIsArray($passedCmd);
        static::assertEquals(['path_to_bin'], array_slice($passedCmd, 0, 1));
        static::assertEquals([$expectedDsn], array_slice($passedCmd, 1, 1));
        static::assertEquals(['--alter', 'DO STUFF TO `table_name`'], array_slice($passedCmd, -2));
    }

    public function testExecuteException()
    {
        static::expectException(RuntimeException::class);

        $process = $this->createMock(Process::class);
        $processFactory = function () use ($process) {
            return $process;
        };

        $onlineChange = $this->getDummyOnlineChange();

        $dbConfig = [
            'host' => '127.0.0.1',
            'port' => '33066',
            'dbname' => 'my_schema',
            'username' => 'user',
            'password' => 'pass',
        ];

        $process->expects(static::once())
            ->method('mustRun')
            ->willThrowException(static::createMock(ProcessFailedException::class));

        $onlineChangeRunner = new OnlineChangeRunner(['path_to_bin'], $processFactory);
        $onlineChangeRunner->execute($dbConfig, $onlineChange);
    }

    private function getDummyOnlineChange(): OnlineChange
    {
        return new class implements OnlineChange {
            public function getName(): string
            {
                return 'table_name';
            }

            public function getOnlineChange(): bool
            {
                return true;
            }

            public function toOnlineAlter(): string
            {
                return 'DO STUFF TO `table_name`';
            }
        };
    }
}
