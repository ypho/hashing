<?php

namespace Algorithms;

use Ypho\Hashing\Algorithms\Blowfish;
use PHPUnit\Framework\TestCase;
use Ypho\Hashing\BcryptException;

class BlowfishTest extends TestCase
{
    /**
     * @throws BcryptException
     */
    public function testCreatingObjectWithRawPassword()
    {
        $bfObject = Blowfish::createFromRawPassword(password: 'jacksparrow');

        $this->assertInstanceOf(Blowfish::class, $bfObject);
        $this->assertEquals(PASSWORD_BCRYPT, $bfObject->getIdentifier());
        $this->assertEquals(PASSWORD_BCRYPT_DEFAULT_COST, $bfObject->getCost());
    }

    /**
     * @throws BcryptException
     */
    public function testCreatingObjectWithRawPasswordAndCost()
    {
        $bfObject = Blowfish::createFromRawPassword(password: 'jacksparrow', cost: 4);

        $this->assertInstanceOf(Blowfish::class, $bfObject);
        $this->assertEquals(4, $bfObject->getCost());
    }

    /**
     * @throws BcryptException
     */
    public function testCreatingObjectWithRawPasswordCostAndSalt()
    {
        $bfObject = Blowfish::createFromRawPassword(password: 'jacksparrow', cost: 7, salt: 'xP0CJ69xMRfBQaZG/P1zDe');

        $this->assertInstanceOf(Blowfish::class, $bfObject);
        $this->assertEquals(PASSWORD_BCRYPT, $bfObject->getIdentifier());
        $this->assertEquals(7, $bfObject->getCost());
        $this->assertEquals('xP0CJ69xMRfBQaZG/P1zDe', $bfObject->getSalt());
        $this->assertEquals('vrI0gjSQFi/24CSJpqv9WCUL2W6QsA2', $bfObject->getHash());
    }

    /**
     * @throws BcryptException
     */
    public function testCreatingObjectWithHash()
    {
        $bfObject = Blowfish::createFromPasswordHash('$2y$07$xP0CJ69xMRfBQaZG/P1zDevrI0gjSQFi/24CSJpqv9WCUL2W6QsA2');

        $this->assertInstanceOf(Blowfish::class, $bfObject);
        $this->assertEquals(PASSWORD_BCRYPT, $bfObject->getIdentifier());
        $this->assertEquals(7, $bfObject->getCost());
        $this->assertEquals('xP0CJ69xMRfBQaZG/P1zDe', $bfObject->getSalt());
        $this->assertEquals('vrI0gjSQFi/24CSJpqv9WCUL2W6QsA2', $bfObject->getHash());
    }

    /**
     * @throws BcryptException
     */
    public function testCreatingObjectWithInvalidCost()
    {
        $this->expectException(BcryptException::class);
        Blowfish::createFromPasswordHash(hash: '$2y$77$xP0CJ69xMRfBQaZG/P1zDevrI0gjSQFi/24CSJpqv9WCUL2W6QsA2');
    }

    public function testCreatingObjectWithRawPasswordAndInvalidSalt()
    {
        $this->expectException(BcryptException::class);
        Blowfish::createFromRawPassword(password: 'jacksparrow', cost: 4, salt: 'invalidsalt');
    }

    public function testCreatingObjectWithHashThatIsInvalid()
    {
        $this->expectException(BcryptException::class);
        $this->expectExceptionMessage('It looks like your hash is invalid');
        Blowfish::createFromPasswordHash(hash: 'ThisHashIsTotallyInvalid______ButItIsExactlyTheCorrectLength');
    }

    public function testCreatingObjectWithHashThatIsTooShort()
    {
        $this->expectException(BcryptException::class);
        $this->expectExceptionMessage('Your hash needs to be exactly 60 characters long');
        Blowfish::createFromPasswordHash(hash: '$2y$05$xP0CJ69xMRfBQaZG/P1zDevrI0gjSQFi/24CSJpqv9WC');
    }

    public function testCreatingObjectWithWrongAlgorithm()
    {
        $this->expectException(BcryptException::class);
        $this->expectExceptionMessage('It looks like you\'re not using the bcrypt / Blowfish algorithms');
        Blowfish::createFromPasswordHash(hash: '$XX$04$xP0CJ69xMRfBQaZG/P1zDevrI0gjSQFi/24CSJpqv9WCUL2W6QsA2');
    }
}
