<?php

namespace Ypho\Hashing\Commands\Benchmark;

use Symfony\Component\Console\Tester\CommandTester;
use Ypho\Hashing\Commands\CommandTestCase;

class Sha256Test extends CommandTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->artisan->resolve(Sha256::class);
    }

    public function testGenerateCommand()
    {
        $command = $this->artisan->find('hash:benchmark:sha256');
        $tester = new CommandTester($command);
        $tester->execute(['amount' => 15]);

        $output = $tester->getDisplay();

        $this->assertEquals(0, $tester->getStatusCode());
        $this->assertStringContainsString('Benchmarking SHA256 hashing', $output);
        $this->assertStringContainsString('Generated 15 hashes', $output);
    }
}
