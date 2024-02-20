<?php

namespace Ypho\Hashing\Commands\BruteForce;

use Ypho\Hashing\Commands\BaseCommand;

class StrongAlgorithms extends BaseCommand
{
    protected $signature = 'hash:bruteforce:strong {hash}';
    protected $description = 'Brute forces a given mcrypt hash against a password file';

    public function handle(): int
    {
        $this->warn('Brute forcing password hashes with mcrypt / blowfish');

        $hashedPassword = $this->argument('hash');

        $foundFiles = $this->getPasswordFiles();
        $passwordFile = $this->choice('Which password file should we use for this brute force check?', $foundFiles);
        $fileHandle = fopen(__DIR__ . '/../../../resources/passwords/' . $passwordFile, 'r');

        $this->start();
        $lineNumber = 0;

        // Walk over each line and check against the hash
        while (feof($fileHandle) === false) {
            $lineNumber++;
            $rawPassword = trim(fgets($fileHandle));

            if (password_verify($rawPassword, $hashedPassword) === true) {
                $this->end();
                $this->info(sprintf('We found the hash in %dms after %d passwords! The password is: %s', $this->runtime(), $lineNumber, $rawPassword));
                return 0;
            }

            if ($lineNumber % 1000 === 0) {
                $this->info(sprintf('Passing %dk passwords, still going strong!', ($lineNumber / 1000)));
            }
        }

        $this->end();
        $this->warn(sprintf('We could not find your password. We tried %d passwords in time: %dms!', $lineNumber, $this->runtime()));
        return 0;
    }
}