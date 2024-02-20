<?php

namespace Ypho\Hashing\Commands\Benchmark;

use Symfony\Component\Console\Tester\CommandTester;
use Ypho\Hashing\Commands\CommandTestCase;

class ArgonTest extends CommandTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->artisan->resolve(Argon::class);
    }

    public function testGenerateCommand()
    {
        $command = $this->artisan->find('hash:benchmark:argon');
        $tester = new CommandTester($command);
        $tester->execute(['amount' => 5]);

        $output = $tester->getDisplay();

        $this->assertEquals(0, $tester->getStatusCode());
        $this->assertStringContainsString('Benchmarking Argon hashing', $output);
        $this->assertStringContainsString('Generated 5 hashes', $output);
    }

    public function testGenerateCommandWithCustomOptions()
    {
        $command = $this->artisan->find('hash:benchmark:argon');
        $tester = new CommandTester($command);

        $tester->execute([
            'command' => $command->getName(),
            'amount' => 2,
            '--m' => 3200,
            '--t' => 6,
            '--p' => 2,
        ]);

        $output = $tester->getDisplay();

        $this->assertEquals(0, $tester->getStatusCode());
        $this->assertStringContainsString('Benchmarking Argon hashing', $output);
        $this->assertStringContainsString('Generated 2 hashes', $output);
        $this->assertStringContainsString('Allowed memory: 3200', $output);
        $this->assertStringContainsString('Allowed time: 6', $output);
        $this->assertStringContainsString('Allowed threads: 2', $output);
    }

    public function testGenerateCommandWithInvalidOptions()
    {
        $command = $this->artisan->find('hash:benchmark:argon');
        $tester = new CommandTester($command);

        $tester->execute([
            'command' => $command->getName(),
            'amount' => 2,
            '--m' => 2,
        ]);

        $output = $tester->getDisplay();

        $this->assertEquals(1, $tester->getStatusCode());
        $this->assertStringContainsString('Something went wrong while benchmarking', $output);
    }
}
