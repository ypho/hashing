<?php

namespace Ypho\Hashing\Commands;

use Illuminate\Console\Command;

class BaseCommand extends Command
{
    protected int $start;
    protected int $end;

    protected function start(): void
    {
        $this->start = (int)(microtime(true) * 1000);
    }

    protected function end(): void
    {
        $this->end = (int)(microtime(true) * 1000);
    }

    protected function runtime(): int
    {
        return $this->end - $this->start;
    }

    protected function lap(): int
    {
        return (int)(microtime(true) * 1000) - $this->start;
    }

    /**
     * @return array<string>
     */
    public function getPasswordFiles(): array
    {
        $foundFiles = scandir(__DIR__ . '/../../resources/passwords');
        array_splice($foundFiles, 0, 4);

        if (!$foundFiles) {
            return [];
        }

        return $foundFiles;
    }
}