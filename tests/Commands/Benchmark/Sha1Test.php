<?php

namespace Ypho\Hashing\Commands\Benchmark;

use Symfony\Component\Console\Tester\CommandTester;
use Ypho\Hashing\Commands\CommandTestCase;

class Sha1Test extends CommandTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->artisan->resolve(Sha1::class);
    }

    public function testGenerateCommand()
    {
        $command = $this->artisan->find('hash:benchmark:sha1');
        $tester = new CommandTester($command);
        $tester->execute(['amount' => 25]);

        $output = $tester->getDisplay();

        $this->assertEquals(0, $tester->getStatusCode());
        $this->assertStringContainsString('Benchmarking SHA1 hashing', $output);
        $this->assertStringContainsString('Generated 25 hashes', $output);
    }
}
