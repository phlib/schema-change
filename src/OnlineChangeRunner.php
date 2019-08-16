<?php
declare(strict_types=1);

namespace Phlib\SchemaChange;

use Symfony\Component\Process\Process;

class OnlineChangeRunner
{
    /**
     * @var array
     */
    private $binPath;

    public function __construct(array $binPath)
    {
        $this->binPath = $binPath;
    }

    public function execute(array $dbConfig, OnlineChange $onlineChange): void
    {
        $cmd = array_merge(
            $this->binPath,
            [$this->buildDsn($dbConfig, $onlineChange->getName())],
            $this->getOptions($onlineChange),
            ['--alter', $onlineChange->toOnlineAlter()]
        );
        $process = new Process($cmd);

        $process->mustRun();
    }

    private function getOptions(OnlineChange $onlineChange): array
    {
        $options = [];
        foreach ($this->buildOptions($onlineChange) as $key => $value) {
            if (!is_numeric($key)) {
                $options[] = $key;
            }
            $options[] = $value;
        }
        return $options;
    }

    protected function buildOptions(OnlineChange $onlineChange): array
    {
        return [
            '--execute',
            '--quiet',
            '--charset' => 'utf8mb4',
            '--alter-foreign-keys-method' => 'none',
            '--force',
            '--max-lag' => '60',
            '--chunk-size-limit' => '0',
            '--critical-load' => 'Threads_running=300',
            '--recursion-method' => 'none',
        ];
    }

    private function buildDsn(array $dbConfig, string $tableName): string
    {
        $dsn = "D={$dbConfig['dbname']},t={$tableName}";
        $dsn .= ",h={$dbConfig['host']},u={$dbConfig['username']},p={$dbConfig['password']}";
        if (isset($dbConfig['port'])) {
            $dsn .= ",P={$dbConfig['port']}";
        }
        return $dsn;
    }
}