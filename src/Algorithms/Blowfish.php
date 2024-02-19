<?php

namespace Ypho\Hashing\Algorithms;

use Ypho\Hashing\BcryptException;

class Blowfish
{
    const string IDENTIFIER = PASSWORD_BCRYPT;

    private ?string $rawPassword;
    private string $hashedPassword;
    private string $identifier = PASSWORD_BCRYPT;
    private int $cost;

    private string $salt;
    private string $hash;

    static function createFromPasswordHash($hashedPassword)
    {
        /**
         * explode() returns
         * [
         *  0 => ""
         *  1 => "2y"
         *  2 => "04"
         *  3 => "ZyC9sPVgPZmaDJ5DMeJa2OjHMDmIU7DBuPa1/22ImqfLZl5F5r/Ni"
         * ]
         * */
        $hashParts = explode('$', $hashedPassword);

        $blowfish = new self();
        $blowfish->setHashedpassword($hashedPassword);
        //$blowfish->identifier = $hashParts[1];
        $blowfish->cost = $hashParts[2];
        //$blowfish->salt = substr($hashParts[3], 0, 22);
        //$blowfish->hash = substr($hashParts[3], -31);


    }

    /**
     * @throws BcryptException
     */
    static function createFromRawPassword(
        string $rawPassword,
        int $cost = PASSWORD_BCRYPT_DEFAULT_COST,
        ?string $salt = null
    ): Blowfish
    {
        /**
         * Since password_hash() ignores any kind of salt, we use crypt() with a salt that is built
         * as follows: $2y$XX%YYYYYYYYYYYYYYYYYYYYYY
         *
         * In this salt string:
         * - XX stands for the cost, this is always a double-digit (between 04 and 31)
         * - YYYY stands for the actual salt, this is a 22-character string
         */
        $saltString = sprintf('$%s$%02d$%s', self::IDENTIFIER, $cost, $salt);

        $hashedPassword = crypt($rawPassword, $saltString);
        return new self($hashedPassword);
    }

    /**
     * @throws BcryptException
     */
    private function validateHashedPassword(): void
    {
        if ($this->cost < 4 || $this->cost > 31) {
            throw new BcryptException('The COST should be between 4 and 31');
        }
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
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

    public function setHashedpassword(string $hashedPassword)
    {
        $this->hashedPassword = $hashedPassword;
    }

    public function setCost(int $cost)
    {
        $this->cost = $cost;

    }
}