<?php

namespace Ypho\Hashing\Commands;

use Illuminate\Console\Command;

/**
 * Example hashes (jacksparrow):
 * md5: d34f9f73de4e49c41bb8cb5ae0a156c3
 * sha1: 6badb3b0c6dcaf1eaee39242535c95f4c5da433b
 * sha256: dadf168d689ccb73f9ff60eaa655070341f829b1 a79c927b1921b909179cbc19
 * bcrypt (default 10): $2y$10$UaBenFFoGVwwur.6A0LafO3wifmnfNrYCgqm2NOSi1RwSg3DUyLN.
 * bcrypt (4): $2y$04$ZyC9sPVgPZmaDJ5DMeJa2OjHMDmIU7DBuPa1/22ImqfLZl5F5r/Ni
 * bcrypt (5): $2y$05$9u6g74sGBXQ0egYvojKJUeAQZQtZFiTHy7g8BPWHXTXTpylPuBiti
 */
class BruteForce extends Command
{
    protected $signature = 'hash:weak:bruteforce {hash}';
    protected $description = 'Brute forces a given hash against a password file (md5/sha1/sha2)';

    public function handle(): int
    {
        $this->warn('Brute forcing password hashes with MD5 / SHA1 / SHA256');

        $hashedPassword = $this->argument('hash');

        $algorithm = match (strlen($hashedPassword)) {
            32 => 'md5',
            40 => 'sha1',
            64 => 'sha256',
            default => null,
        };

        if ($algorithm === null) {
            $this->error('Your hash is not a correct size. You need a hash of 32/40/64 characters for MD5/SHA1/SHA256.');
            return 1;
        }

        $foundFiles = scandir(__DIR__ . '/../../resources/passwords');
        array_splice($foundFiles, 0, 4);

        if (count($foundFiles) === 0) {
            $this->error('Please place your password files in the resources/passwords directory.');
            return 1;
        }

        // Choose the password file to use
        $passwordFile = $this->choice('Which password file should we use for this brute force check?', $foundFiles);
        $fileHandle = fopen(__DIR__ . '/../../resources/passwords/' . $passwordFile, 'r');

        $start = microtime(true);
        $lineNumber = 0;

        // Walk over each line and check against the hash
        while (feof($fileHandle) === false) {
            $lineNumber++;

            $rawPassword = trim(fgets($fileHandle));

            if ($hashedPassword === $this->generateHash($algorithm, $rawPassword)) {
                $timeElapsed = (int)((microtime(true) - $start) * 1000);

                $this->info(sprintf('We found the hash in %dms after %d passwords! The password is: %s', $timeElapsed, $lineNumber, $rawPassword));
                return 0;
            }

            if ($lineNumber % 1000000 === 0) {
                $formattedLineNumber = sprintf('Passing %d million passwords, still going strong!', ($lineNumber / 1000000));
                $this->info($formattedLineNumber);
            }
        }

        $timeElapsed = (int)((microtime(true) - $start) * 1000);
        $this->warn(sprintf('We could not find your password. We tried %d passwords in time: %dms!', $lineNumber, $timeElapsed));
        return 0;
    }

    private function generateHash(string $algorithm, string $password): string
    {
        return hash($algorithm, $password);
    }
}