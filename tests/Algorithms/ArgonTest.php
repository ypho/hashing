<?php

namespace Algorithms;

use Ypho\Hashing\Algorithms\Argon;
use PHPUnit\Framework\TestCase;
use Ypho\Hashing\Exceptions\ArgonException;

class ArgonTest extends TestCase
{
    /**
     * @throws ArgonException
     */
    public function testCreatingObjectWithRawPassword()
    {
        $bfObject = Argon::createFromRawPassword(password: 'jacksparrow');

        $this->assertInstanceOf(Argon::class, $bfObject);
        $this->assertEquals(19, $bfObject->getVersion());
    }

    /**
     * @throws ArgonException
     */
    public function testCreatingObjectWithRawPasswordAndOtherIdentifier()
    {
        $bfObject = Argon::createFromRawPassword(password: 'jacksparrow', algorithm: PASSWORD_ARGON2ID);

        $this->assertInstanceOf(Argon::class, $bfObject);
        $this->assertEquals(PASSWORD_ARGON2ID, $bfObject->getIdentifier());
    }

    /**
     * @throws ArgonException
     */
    public function testCreatingObjectWithRawPasswordAndInvalidIdentifier()
    {
        $this->expectException(ArgonException::class);
        Argon::createFromRawPassword(password: 'jacksparrow', algorithm: 'InvalidAlgorithmIdentifier');
    }

    /**
     * @throws ArgonException
     */
    public function testCreatingObjectWithRawPasswordAndCustomMemoryCost()
    {
        $customMemoryCost = 131072;
        $bfObject = Argon::createFromRawPassword(password: 'jacksparrow', memoryCost: $customMemoryCost);

        $this->assertInstanceOf(Argon::class, $bfObject);
        $this->assertEquals($customMemoryCost, $bfObject->getMemoryCost());
    }

    /**
     * @throws ArgonException
     */
    public function testCreatingObjectWithRawPasswordAndInvalidMemoryCost()
    {
        $this->expectException(ArgonException::class);
        Argon::createFromRawPassword(password: 'jacksparrow', memoryCost: 4);
    }

    /**
     * @throws ArgonException
     */
    public function testCreatingObjectWithRawPasswordAndCustomTimeCost()
    {
        $customTimeCost = 8;
        $bfObject = Argon::createFromRawPassword(password: 'jacksparrow', timeCost: $customTimeCost);

        $this->assertInstanceOf(Argon::class, $bfObject);
        $this->assertEquals($customTimeCost, $bfObject->getTimeCost());
    }

    /**
     * @throws ArgonException
     */
    public function testCreatingObjectWithRawPasswordAndInvalidTimeCost()
    {
        $this->expectException(ArgonException::class);
        Argon::createFromRawPassword(password: 'jacksparrow', timeCost: 0);
    }

    /**
     * @throws ArgonException
     */
    public function testCreatingObjectWithRawPasswordAndCustomThreads()
    {
        $threads = 4;
        $bfObject = Argon::createFromRawPassword(password: 'jacksparrow', threads: $threads);

        $this->assertInstanceOf(Argon::class, $bfObject);
        $this->assertEquals($threads, $bfObject->getThreads());
    }

    /**
     * @throws ArgonException
     */
    public function testCreatingObjectWithRawPasswordAndInvalidThreads()
    {
        $this->expectException(ArgonException::class);
        Argon::createFromRawPassword(password: 'jacksparrow', threads: 0);
    }

    /**
     * @throws ArgonException
     */
    public function testCreatingObjectFromHash()
    {
        $passwordHash = '$argon2i$v=19$m=65536,t=4,p=1$cG95d0NpT3ZUYlp3RDdJYw$bomqAkyuAnwhxsoghgIASAl8zomckPX+aIbDnIDFggg';

        $argonObject = new Argon($passwordHash);
        $this->assertInstanceOf(Argon::class, $argonObject);

        $this->assertEquals($passwordHash, $argonObject->getFullHash());
        $this->assertEquals(PASSWORD_ARGON2I, $argonObject->getIdentifier());
        $this->assertEquals(19, $argonObject->getVersion());
        $this->assertEquals(65536, $argonObject->getMemoryCost());
        $this->assertEquals(4, $argonObject->getTimeCost());
        $this->assertEquals(1, $argonObject->getThreads());
        $this->assertEquals('cG95d0NpT3ZUYlp3RDdJYw', $argonObject->getSalt());
        $this->assertEquals('bomqAkyuAnwhxsoghgIASAl8zomckPX+aIbDnIDFggg', $argonObject->getHash());
    }

    public function testCreatingObjectFromHashThatHasInvalidTimeCost()
    {
        $this->expectException(ArgonException::class);
        $this->expectExceptionMessage('You have to allow the algorithm to run for at least 1 second');

        new Argon('$argon2i$v=19$m=65536,t=0,p=1$cG95d0NpT3ZUYlp3RDdJYw$bomqAkyuAnwhxsoghgIASAl8zomckPX+aIbDnIDFggg');
    }

    public function testCreatingObjectFromHashThatHasInvalidMemoryCost()
    {
        $this->expectException(ArgonException::class);
        $this->expectExceptionMessage('You have to allocate at least 8 kibibytes of memory');

        new Argon('$argon2i$v=19$m=4,t=4,p=1$cG95d0NpT3ZUYlp3RDdJYw$bomqAkyuAnwhxsoghgIASAl8zomckPX+aIbDnIDFggg');
    }

    public function testCreatingObjectFromHashThatHasInvalidThreads()
    {
        $this->expectException(ArgonException::class);
        $this->expectExceptionMessage('You have to allocate at least 1 thread');

        new Argon('$argon2i$v=19$m=65536,t=4,p=0$cG95d0NpT3ZUYlp3RDdJYw$bomqAkyuAnwhxsoghgIASAl8zomckPX+aIbDnIDFggg');
    }

    public function testCreatingObjectFromHashThatHasInvalidAlgorithmIdentifier()
    {
        $this->expectException(ArgonException::class);
        $this->expectExceptionMessage('It looks like you\'re not using the Argon2i or Argon2id algorithm');

        new Argon('$INVALID$v=19$m=65536,t=4,p=1$cG95d0NpT3ZUYlp3RDdJYw$bomqAkyuAnwhxsoghgIASAl8zomckPX+aIbDnIDFggg');
    }
}
