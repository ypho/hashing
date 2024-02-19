<?php

namespace Ypho\Hashing\Commands\BruteForce;

use Illuminate\Console\Command;

class BruteForceCommand extends Command
{
    public function getPasswordFiles()
    {
        $foundFiles = scandir(__DIR__ . '/../../resources/passwords');
        array_splice($foundFiles, 0, 4);

        if ($foundFiles === false || count($foundFiles) === 0) {
            return [];
        }

        return $foundFiles;
    }

}