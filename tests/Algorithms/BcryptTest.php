<?php

namespace Algorithms;

use PHPUnit\Framework\TestCase;
use Ypho\Hashing\Algorithms\Bcrypt;
use Ypho\Hashing\Exceptions\BcryptException;

class BcryptTest extends TestCase
{
    /**
     * @throws BcryptException
     */
    public function testCreatingObjectWithRawPassword()
    {
        $bcryptObject = Bcrypt::createFromRawPassword(password: 'jacksparrow');

        $this->assertInstanceOf(Bcrypt::class, $bcryptObject);
        $this->assertEquals(PASSWORD_BCRYPT, $bcryptObject->getIdentifier());
        $this->assertEquals(PASSWORD_BCRYPT_DEFAULT_COST, $bcryptObject->getCost());
    }

    /**
     * @throws BcryptException
     */
    public function testCreatingObjectWithRawPasswordAndCost()
    {
        $bcryptObject = Bcrypt::createFromRawPassword(password: 'jacksparrow', cost: 4);

        $this->assertInstanceOf(Bcrypt::class, $bcryptObject);
        $this->assertEquals(4, $bcryptObject->getCost());
    }

    /**
     * @throws BcryptException
     */
    public function testCreatingObjectWithRawPasswordCostAndSalt()
    {
        $bcryptObject = Bcrypt::createFromRawPassword(password: 'jacksparrow', cost: 4, salt: 'SxExysyI8EF4G0eIszWQKu');

        $this->assertInstanceOf(Bcrypt::class, $bcryptObject);

        $this->assertEquals('$2y$04$SxExysyI8EF4G0eIszWQKuQ//PfYSN66QUb8m/TFHj7wPNHTwlQZe', $bcryptObject->getFullHash());
        $this->assertEquals(PASSWORD_BCRYPT, $bcryptObject->getIdentifier());
        $this->assertEquals(4, $bcryptObject->getCost());
        $this->assertEquals('SxExysyI8EF4G0eIszWQKu', $bcryptObject->getSalt());
        $this->assertEquals('Q//PfYSN66QUb8m/TFHj7wPNHTwlQZe', $bcryptObject->getHash());
    }

    public function testCreatingObjectWithHash()
    {
        $bcryptObject = new Bcrypt('$2y$07$xP0CJ69xMRfBQaZG/P1zDevrI0gjSQFi/24CSJpqv9WCUL2W6QsA2');

        $this->assertInstanceOf(Bcrypt::class, $bcryptObject);
        $this->assertEquals('$2y$07$xP0CJ69xMRfBQaZG/P1zDevrI0gjSQFi/24CSJpqv9WCUL2W6QsA2', $bcryptObject->getFullHash());
        $this->assertEquals(PASSWORD_BCRYPT, $bcryptObject->getIdentifier());
        $this->assertEquals(7, $bcryptObject->getCost());
        $this->assertEquals('xP0CJ69xMRfBQaZG/P1zDe', $bcryptObject->getSalt());
        $this->assertEquals('vrI0gjSQFi/24CSJpqv9WCUL2W6QsA2', $bcryptObject->getHash());
    }

    public function testCreatingObjectWithInvalidCost()
    {
        $this->expectException(BcryptException::class);
        new Bcrypt(hash: '$2y$77$xP0CJ69xMRfBQaZG/P1zDevrI0gjSQFi/24CSJpqv9WCUL2W6QsA2');
    }

    public function testCreatingObjectWithRawPasswordAndInvalidSalt()
    {
        $this->expectException(BcryptException::class);
        Bcrypt::createFromRawPassword(password: 'jacksparrow', cost: 4, salt: 'invalidsalt');
    }

    public function testCreatingObjectWithHashThatIsInvalid()
    {
        $this->expectException(BcryptException::class);
        $this->expectExceptionMessage('It looks like your hash is invalid');
        new Bcrypt(hash: 'ThisHashIsTotallyInvalid______ButItIsExactlyTheCorrectLength');
    }

    public function testCreatingObjectWithHashThatIsTooShort()
    {
        $this->expectException(BcryptException::class);
        $this->expectExceptionMessage('Your hash needs to be exactly 60 characters long');
        new Bcrypt(hash: '$2y$05$xP0CJ69xMRfBQaZG/P1zDevrI0gjSQFi/24CSJpqv9WC');
    }

    public function testCreatingObjectWithWrongAlgorithm()
    {
        $this->expectException(BcryptException::class);
        $this->expectExceptionMessage('It looks like you\'re not using the bcrypt / Blowfish algorithms');
        new Bcrypt(hash: '$XX$04$xP0CJ69xMRfBQaZG/P1zDevrI0gjSQFi/24CSJpqv9WCUL2W6QsA2');
    }
}
