<?php

namespace Ypho\Hashing\Commands\BruteForce;

use Illuminate\Console\Command;

class BruteForceCommand extends Command
{
    /**
     * @return array<string>
     */
    public function getPasswordFiles(): array
    {
        $foundFiles = scandir(__DIR__ . '/../../../resources/passwords');
        array_splice($foundFiles, 0, 4);

        if (!$foundFiles) {
            return [];
        }

        return $foundFiles;
    }
}