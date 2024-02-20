<?php

namespace Ypho\Hashing\Commands\Benchmark;

use Symfony\Component\Console\Tester\CommandTester;
use Ypho\Hashing\Commands\CommandTestCase;

class BcryptTest extends CommandTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->artisan->resolve(Bcrypt::class);
    }

    public function testGenerateCommand()
    {
        $command = $this->artisan->find('hash:benchmark:bcrypt');
        $tester = new CommandTester($command);
        $tester->execute(['amount' => 3]);

        $output = $tester->getDisplay();

        $this->assertEquals(0, $tester->getStatusCode());
        $this->assertStringContainsString('Benchmarking bcrypt hashing', $output);
        $this->assertStringContainsString('Generated 3 hashes', $output);
    }

    public function testGenerateCommandWithCustomOptions()
    {
        $command = $this->artisan->find('hash:benchmark:bcrypt');
        $tester = new CommandTester($command);

        $tester->execute([
            'command' => $command->getName(),
            'amount' => 2,
            '--cost' => 5,
        ]);

        $output = $tester->getDisplay();

        $this->assertEquals(0, $tester->getStatusCode());
        $this->assertStringContainsString('Benchmarking bcrypt hashing', $output);
        $this->assertStringContainsString('Generated 2 hashes', $output);
        $this->assertStringContainsString('Allowed cost: 5', $output);
    }

    public function testGenerateCommandWithInvalidOptions()
    {
        $command = $this->artisan->find('hash:benchmark:bcrypt');
        $tester = new CommandTester($command);

        $tester->execute([
            'command' => $command->getName(),
            'amount' => 2,
            '--cost' => 1,
        ]);

        $output = $tester->getDisplay();

        $this->assertEquals(1, $tester->getStatusCode());
        $this->assertStringContainsString('Something went wrong while benchmarking', $output);
    }
}
