<?php

namespace Ypho\Hashing\Commands\Benchmark;

use Throwable;
use Ypho\Hashing\Commands\BaseCommand;

class Bcrypt extends BaseCommand
{
    protected $signature = 'hash:benchmark:bcrypt {amount} {--cost=}';
    protected $description = 'Benchmarks the bcrypt algorithm by generating a certain amount of hashes. Tweak the cost-option to see the performance difference.';

    public function handle(): int
    {
        $this->warn('Benchmarking bcrypt hashing');

        $amount = (int)$this->argument('amount');
        $cost = (int)($this->option('cost') ?? PASSWORD_BCRYPT_DEFAULT_COST);

        $this->start();

        try {
            for ($i = 0; $i < $amount; $i++) {
                \Ypho\Hashing\Algorithms\Bcrypt::createFromRawPassword(
                    password: random_bytes(16),
                    cost: $cost
                );
            }
        } catch (Throwable $throwable) {
            $this->error('Something went wrong while benchmarking');
            $this->error($throwable->getMessage());
            return 1;
        }

        $this->end();
        $this->info(sprintf('Generated %d hashes in %dms', $amount, $this->runtime()));
        $this->info(sprintf('Allowed cost: %d', $cost));

        return 0;
    }
}