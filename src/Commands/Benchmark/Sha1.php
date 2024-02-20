<?php

namespace Ypho\Hashing\Commands\Benchmark;

use Throwable;
use Ypho\Hashing\Commands\BaseCommand;

class Sha1 extends BaseCommand
{
    protected $signature = 'hash:benchmark:sha1 {amount}';
    protected $description = 'Benchmarks the SHA algorithm by generating a certain amount of hashes';

    public function handle(): int
    {
        $this->warn('Benchmarking SHA1 hashing');

        $amount = (int)$this->argument('amount');

        $this->start();

        try {
            for ($i = 0; $i < $amount; $i++) {
                /** @phpstan-ignore-next-line */
                hash('sha1', random_bytes(16));
            }
        } catch (Throwable $throwable) {
            $this->error($throwable->getMessage());
            return 1;
        }

        $this->end();
        $this->info(sprintf('Generated %d hashes in %dms.', $amount, $this->runtime()));

        return 0;
    }
}