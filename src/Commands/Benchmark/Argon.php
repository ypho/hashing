<?php

namespace Ypho\Hashing\Commands\Benchmark;

use Throwable;
use Ypho\Hashing\Commands\BaseCommand;

class Argon extends BaseCommand
{
    protected $signature = 'hash:benchmark:argon {amount} {--m=} {--t=} {--p=}';
    protected $description = 'Benchmarks the Argon algorithm by generating a certain amount of hashes. Tweak the memory (m), time (t) and threads (p) to see the performance difference.';

    public function handle(): int
    {
        $this->warn('Benchmarking Argon hashing');

        $amount = (int)$this->argument('amount');
        $memory = (int)($this->option('m') ?? PASSWORD_ARGON2_DEFAULT_MEMORY_COST);
        $time = (int)($this->option('t') ?? PASSWORD_ARGON2_DEFAULT_TIME_COST);
        $threads = (int)($this->option('p') ?? PASSWORD_ARGON2_DEFAULT_THREADS);

        $this->start();

        try {
            for ($i = 0; $i < $amount; $i++) {
                \Ypho\Hashing\Algorithms\Argon::createFromRawPassword(
                    password: random_bytes(16),
                    memoryCost: $memory,
                    timeCost: $time,
                    threads: $threads
                );
            }
        } catch (Throwable $throwable) {
            $this->error('Something went wrong while benchmarking');
            $this->error($throwable->getMessage());
            return 1;
        }

        $this->end();

        $this->info(sprintf('Generated %d hashes in %dms', $amount, $this->runtime()));
        $this->info(sprintf('Allowed memory: %d', $memory));
        $this->info(sprintf('Allowed time: %d', $time));
        $this->info(sprintf('Allowed threads: %d', $threads));

        return 0;
    }
}