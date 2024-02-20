<?php

namespace Ypho\Hashing\Algorithms;

use Throwable;
use Ypho\Hashing\Exceptions\BcryptException;

class Bcrypt
{
    private string $hashedPassword;
    private int $cost;
    private string $salt;
    private string $hash;

    /**
     * @throws BcryptException
     */
    public function __construct(string $hash)
    {
        $this->hashedPassword = $hash;
        $this->preValidate();

        /**
         * explode() returns
         * [
         *  0 => ""
         *  1 => "2y"
         *  2 => "04"
         *  3 => "ZyC9sPVgPZmaDJ5DMeJa2OjHMDmIU7DBuPa1/22ImqfLZl5F5r/Ni"
         * ]
         * */
        $hashParts = explode('$', $hash);

        $this->cost = (int)$hashParts[2];
        $this->salt = substr($hashParts[3], 0, 22);
        $this->hash = substr($hashParts[3], -31);

        // Do some final checks on the extracted information
        $this->postValidate();
    }

    /**
     * Since password_hash() ignores any kind of custom salt, we use crypt() if a salt is given, then we
     * build the salt string as follows: $2y$XX%YYYYYYYYYYYYYYYYYYYYYY
     *
     * In this salt string:
     * - "2y" stands for the bcrypt Blowfish algorithm
     * - "XX" stands for the cost, this is always a double-digit (between 04 and 31)
     * - "YYYY..." stands for the actual salt, this is a 22-character string
     *
     * If no salt is given, we use the regular password_hash() method, which will generate
     * its own random salt.
     *
     * @throws BcryptException
     */
    static function createFromRawPassword(
        string  $password,
        int     $cost = PASSWORD_BCRYPT_DEFAULT_COST,
        ?string $salt = null
    ): Bcrypt
    {
        try {
            if ($salt === null) {
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => $cost]);
            } else {
                $saltString = sprintf('$%s$%02d$%s', PASSWORD_BCRYPT, $cost, $salt);
                $hashedPassword = crypt($password, $saltString);
            }

            if (password_verify($password, $hashedPassword) === false) {
                throw new BcryptException('Your password is invalid, this can be either due to an invalid salt, or an invalid cost.');
            }

            return new self($hashedPassword);
        } catch (Throwable $throwable) {
            throw new BcryptException($throwable->getMessage());
        }
    }

    /**
     * @throws BcryptException
     */
    private function preValidate(): void
    {
        if (strlen($this->hashedPassword) !== 60) {
            throw new BcryptException('Your hash needs to be exactly 60 characters long');
        }

        if (substr_count($this->hashedPassword, '$') !== 3) {
            throw new BcryptException('It looks like your hash is invalid');
        }

        if (str_starts_with($this->hashedPassword, '$2y$') === false) {
            throw new BcryptException('It looks like you\'re not using the bcrypt / Blowfish algorithms');
        }
    }

    /**
     * @throws BcryptException
     */
    private function postValidate(): void
    {
        if ($this->cost < 4 || $this->cost > 31) {
            throw new BcryptException('The COST should be between 4 and 31');
        }
    }

    public function getFullHash(): string
    {
        return $this->hashedPassword;
    }

    public function getIdentifier(): string
    {
        return PASSWORD_BCRYPT;
    }

    public function getCost(): int
    {
        return $this->cost;
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