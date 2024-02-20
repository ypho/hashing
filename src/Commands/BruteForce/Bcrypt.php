<?php

namespace Ypho\Hashing\Commands\BruteForce;

use Throwable;

class Bcrypt extends BruteForceCommand
{
    protected $signature = 'hash:bruteforce:bcrypt {hash}';
    protected $description = 'Brute forces a given mcrypt hash against a password file';

    public function handle(): int
    {
        $this->warn('Brute forcing password hashes with mcrypt / blowfish');

        $hashedPassword = $this->argument('hash');

        try {
            $hashBreakdown = new \Ypho\Hashing\Algorithms\Bcrypt($hashedPassword);
        } catch (Throwable $throwable) {
            $this->error($throwable->getMessage());
            return 1;
        }

        $foundFiles = $this->getPasswordFiles();
        $passwordFile = $this->choice('Which password file should we use for this brute force check?', $foundFiles);
        $fileHandle = fopen(__DIR__ . '/../../../resources/passwords/' . $passwordFile, 'r');

        $start = microtime(true);
        $lineNumber = 0;

        // Walk over each line and check against the hash
        while (feof($fileHandle) === false) {
            $lineNumber++;
            $rawPassword = trim(fgets($fileHandle));

            if (password_verify($rawPassword, $hashBreakdown->getFullHash()) === true) {
                $timeElapsed = (int)((microtime(true) - $start) * 1000);

                $this->info(sprintf('We found the hash in %dms after %d passwords! The password is: %s', $timeElapsed, $lineNumber, $rawPassword));
                return 0;
            }
        }

        $timeElapsed = (int)((microtime(true) - $start) * 1000);
        $this->warn(sprintf('We could not find your password. We tried %d passwords in time: %dms!', $lineNumber, $timeElapsed));
        return 0;
    }
}