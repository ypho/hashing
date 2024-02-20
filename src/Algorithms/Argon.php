<?php

namespace Ypho\Hashing\Algorithms;

use Throwable;
use Ypho\Hashing\Exceptions\ArgonException;

class Argon
{
    private string $fullHash;

    private string $identifier;
    private int $version;

    private int $memoryCost;
    private int $timeCost;
    private int $threads;

    private string $salt;
    private string $hash;

    /**
     * @throws ArgonException
     */
    public function __construct(string $hash)
    {
        $this->fullHash = $hash;
        $this->preValidate();

        /**
         * explode() returns
         * [
         *  0 => ""
         *  1 => "argon2i"
         *  2 => "v=19"
         *  3 => "m=65536,t=4,p=1"
         *  4 => "dG8vY3FhUHhIdzJ3L00wdA"
         *  5 => "Bdjm0+0xdx7Y3OTgnwwIsEky/YWqIdYHrcf+P6hkVP0"
         * ]
         */
        $hashParts = explode('$', $hash);

        $this->identifier = $hashParts[1];
        $this->version = (int)str_replace('v=', '', $hashParts[2]);

        $this->salt = $hashParts[4];
        $this->hash = $hashParts[5];

        /**
         * Determine the calculation parameters
         * [
         *  0 => "m=65536"
         *  1 => "t=4"
         *  2 => "p=1"
         * ]
         */
        $calculationParts = explode(',', $hashParts[3]);
        $this->memoryCost = (int)str_replace('m=', '', $calculationParts[0]);
        $this->timeCost = (int)str_replace('t=', '', $calculationParts[1]);
        $this->threads = (int)str_replace('p=', '', $calculationParts[2]);

        // Do some final checks on the extracted information
        $this->postValidate();
    }

    /**
     * For Argon2, it is not possible to provide your own salt, therefore the generated hashes are always
     * unique, because of their own unique salt.
     *
     * @throws ArgonException
     */
    static function createFromRawPassword(
        string $password,
        string $algorithm = PASSWORD_ARGON2I,
        int    $memoryCost = PASSWORD_ARGON2_DEFAULT_MEMORY_COST,
        int    $timeCost = PASSWORD_ARGON2_DEFAULT_TIME_COST,
        int    $threads = PASSWORD_ARGON2_DEFAULT_THREADS
    ): self
    {
        try {
            $hashedPassword = password_hash(password: $password, algo: $algorithm, options: [
                'memory_cost' => $memoryCost,
                'time_cost' => $timeCost,
                'threads' => $threads,
            ]);

            return new self($hashedPassword);
        } catch (Throwable $throwable) {
            throw new ArgonException($throwable->getMessage());
        }
    }

    /**
     * @throws ArgonException
     */
    private function preValidate(): void
    {
        if (str_starts_with($this->fullHash, '$argon2i$') === false && str_starts_with($this->fullHash, '$argon2id$') === false) {
            throw new ArgonException('It looks like you\'re not using the Argon2i or Argon2id algorithm');
        }
    }

    /**
     * @throws ArgonException
     */
    private function postValidate(): void
    {
        if ($this->memoryCost < 8) {
            throw new ArgonException('You have to allocate at least 8 kibibytes of memory');
        }

        if ($this->timeCost < 1) {
            throw new ArgonException('You have to allow the algorithm to run for at least 1 second');
        }

        if ($this->threads < 1) {
            throw new ArgonException('You have to allocate at least 1 thread');
        }
    }

    public function getFullHash(): string
    {
        return $this->fullHash;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getVersion(): int
    {
        return $this->version;
    }

    public function getMemoryCost(): int
    {
        return $this->memoryCost;
    }

    public function getTimeCost(): int
    {
        return $this->timeCost;
    }

    public function getThreads(): int
    {
        return $this->threads;
    }

    public function getSalt(): string
    {
        return $this->salt;
    }

    public function getHash(): string
    {
        return $this->hash;
    }
}