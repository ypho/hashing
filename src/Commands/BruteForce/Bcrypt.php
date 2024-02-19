<?php

namespace Ypho\Hashing\Commands\BruteForce;

use Throwable;
use Ypho\Hashing\Algorithms\Blowfish;

class Bcrypt extends BruteForceCommand
{
    protected $signature = 'hash:bruteforce:bcrypt {hash}';
    protected $description = 'Brute forces a given mcrypt hash against a password file';

    /**
     * Run this command with the hash in single quotes
     *
     * 1 - The algorithm identifier
     * 2 - Cost
     * 3 - 22 character salt
     * 4 - 31 character hash
     *
     *             [1 ] [2]  [3                   ] [4                            ]
     * bcrypt(10): $2y$ 10 $ UaBenFFoGVwwur.6A0LafO 3wifmnfNrYCgqm2NOSi1RwSg3DUyLN.
     * bcrypt (4): $2y$ 04 $ ZyC9sPVgPZmaDJ5DMeJa2O jHMDmIU7DBuPa1/22ImqfLZl5F5r/Ni
     * bcrypt (5): $2y$ 05 $ 9u6g74sGBXQ0egYvojKJUe AQZQtZFiTHy7g8BPWHXTXTpylPuBiti
     */
    public function handle(): int
    {

        dd(Blowfish::create(rawPassword: 'jacksparrow', cost: 4, salt: 'U./4uq4k3r6QWm1UQh9BAu'));

        $this->warn('Brute forcing password hashes with mcrypt');

        //crypt()
        $hashedPassword = $this->argument('hash');

        try {
            $hashBreakdown = new Blowfish($hashedPassword);
        } catch (Throwable $throwable) {
            $this->error($throwable->getMessage());
            return 1;
        }

        $foundFiles = $this->getPasswordFiles();
        $passwordFile = $this->choice('Which password file should we use for this brute force check?', $foundFiles);
        $fileHandle = fopen(__DIR__ . '/../../resources/passwords/' . $passwordFile, 'r');

        $start = microtime(true);
        $lineNumber = 0;

        // Walk over each line and check against the hash
        while (feof($fileHandle) === false) {
            $lineNumber++;

            $rawPassword = trim(fgets($fileHandle));

            if ($hashedPassword === $this->generateHash($rawPassword, $hashBreakdown)) {
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

    private function generateHash(string $password, Blowfish $bcryptObject): string
    {
        return password_hash($password, $bcryptObject->getIdentifier(), [
            'cost' => $bcryptObject->getCost(),
            'salt' => $bcryptObject->getSalt(),
        ]);
    }

    /**
     * 1 - The algorithm identifier
     * 2 - Cost
     * 3 - 22 character salt
     * 4 - 31 character hash
     *
     *             [1 ] [2]  [3                   ] [4                            ]
     * bcrypt(10): $2y$ 10 $ UaBenFFoGVwwur.6A0LafO 3wifmnfNrYCgqm2NOSi1RwSg3DUyLN.
     * bcrypt (4): $2y$ 04 $ ZyC9sPVgPZmaDJ5DMeJa2O jHMDmIU7DBuPa1/22ImqfLZl5F5r/Ni
     * bcrypt (5): $2y$ 05 $ 9u6g74sGBXQ0egYvojKJUe AQZQtZFiTHy7g8BPWHXTXTpylPuBiti
     */

    private function getHashParts(string $hashedPassword): Blowfish
    {
        try {
            return new Blowfish($hashedPassword);
        } catch (Throwable $throwable) {

        }
    }
}