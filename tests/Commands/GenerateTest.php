<?php

namespace Ypho\Hashing\Commands;

use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Tester\CommandTester;

class GenerateTest extends CommandTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->artisan->resolve(Generate::class);
    }

    public function testGenerateCommand()
    {
        $command = $this->artisan->find('hash:generate');
        $tester = new CommandTester($command);
        $tester->execute(['password' => 'password']);

        $output = $tester->getDisplay();

        $this->assertEquals(0, $tester->getStatusCode());
        $this->assertStringContainsString('Generating hashes for any given password', $output);
        $this->assertStringContainsString('Generated 11 hashes for password: password', $output);
    }

    public function testGenerateCommandWithoutPasswordArgument()
    {
        $command = $this->artisan->find('hash:generate');
        $tester = new CommandTester($command);

        $this->expectException(InvalidArgumentException::class);
        $tester->execute(['wrong' => 'argument']);
    }

    public function testGenerateCommandWithoutArguments()
    {
        $command = $this->artisan->find('hash:generate');
        $tester = new CommandTester($command);

        $this->expectException(RuntimeException::class);
        $tester->execute([]);
    }
}
