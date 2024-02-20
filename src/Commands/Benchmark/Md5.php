<?php

namespace Ypho\Hashing\Commands\Benchmark;

use Throwable;
use Ypho\Hashing\Commands\BaseCommand;

class Md5 extends BaseCommand
{
    protected $signature = 'hash:benchmark:md5 {amount}';
    protected $description = 'Benchmarks the MD5 algorithm by generating a certain amount of hashes';

    public function handle(): int
    {
        $this->warn('Benchmarking MD5 hashing');

        $amount = (int)$this->argument('amount');

        $this->start();

        try {
            for ($i = 0; $i < $amount; $i++) {
                /** @phpstan-ignore-next-line */
                md5(random_bytes(16));
            }
        } catch (Throwable $throwable) {
            $this->error('Something went wrong while benchmarking');
            $this->error($throwable->getMessage());
            return 1;
        }

        $this->end();
        $this->info(sprintf('Generated %d hashes in %dms', $amount, $this->runtime()));

        return 0;
    }
}