<?php

namespace Ypho\Hashing\Commands\BruteForce;

use Ypho\Hashing\Commands\BaseCommand;

class WeakAlgorithms extends BaseCommand
{
    protected $signature = 'hash:bruteforce:weak {hash}';
    protected $description = 'Brute forces a given hash against a password file (md5/sha1/sha2)';

    public function handle(): int
    {
        $this->warn('Brute forcing password hashes with MD5 / SHA1 / SHA256');

        $hashedPassword = $this->argument('hash');

        $algorithm = match (strlen($hashedPassword)) {
            32 => 'md5',
            40 => 'sha1',
            64 => 'sha256',
            96 => 'sha384',
            128 => 'sha512',
            default => null,
        };

        if ($algorithm === null) {
            $this->error('Your hash is not a correct size. You need a hash of 32/40/64 characters for MD5/SHA1/SHA256.');
            return 1;
        }

        $foundFiles = $this->getPasswordFiles();
        $passwordFile = $this->choice('Which password file should we use for this brute force check?', $foundFiles);
        $fileHandle = fopen(__DIR__ . '/../../../resources/passwords/' . $passwordFile, 'r');

        $this->start();
        $lineNumber = 0;

        // Walk over each line and check against the hash
        while (feof($fileHandle) === false) {
            $lineNumber++;

            $rawPassword = trim(fgets($fileHandle));

            if ($hashedPassword === hash($algorithm, $rawPassword)) {
                $this->end();
                $this->info(sprintf('We found the hash in %dms after %d passwords! The password is: %s', $this->runtime(), $lineNumber, $rawPassword));
                return 0;
            }

            if ($lineNumber % 1000000 === 0) {
                $this->info(sprintf('Passing %d million passwords, still going strong!', ($lineNumber / 1000000)));
            }
        }

        $this->end();
        $this->warn(sprintf('We could not find your password. We tried %d passwords in time: %dms!', $lineNumber, $this->runtime()));
        return 0;
    }
}