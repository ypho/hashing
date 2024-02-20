<?php

namespace Ypho\Hashing\Commands\Benchmark;

use Symfony\Component\Console\Tester\CommandTester;
use Ypho\Hashing\Commands\CommandTestCase;

class Md5Test extends CommandTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->artisan->resolve(Md5::class);
    }

    public function testGenerateCommand()
    {
        $command = $this->artisan->find('hash:benchmark:md5');
        $tester = new CommandTester($command);
        $tester->execute(['amount' => 50]);

        $output = $tester->getDisplay();

        $this->assertEquals(0, $tester->getStatusCode());
        $this->assertStringContainsString('Benchmarking MD5 hashing', $output);
        $this->assertStringContainsString('Generated 50 hashes', $output);
    }
}
