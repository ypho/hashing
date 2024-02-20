<?php

namespace Ypho\Hashing\Commands;

use Throwable;
use Ypho\Hashing\Algorithms\Argon;
use Ypho\Hashing\Algorithms\Bcrypt;

class Generate extends BaseCommand
{
    protected $signature = 'hash:generate {password}';
    protected $description = 'Generates hashes for a given password';

    public function handle(): int
    {
        $this->comment('Generating hashes for any given password');

        $rawPassword = $this->argument('password');

        $this->start();

        try {
            $this->info(sprintf('MD5                 : %s', hash('md5', $rawPassword)));
            $this->info(sprintf('SHA1                : %s', hash('sha1', $rawPassword)));
            $this->info(sprintf('SHA256              : %s', hash('sha256', $rawPassword)));

            $this->info(sprintf('Bcrypt (default)    : %s', Bcrypt::createFromRawPassword(password: $rawPassword)->getFullHash()));
            $this->info(sprintf('Bcrypt (cost = 4)   : %s', Bcrypt::createFromRawPassword(password: $rawPassword, cost: 4)->getFullHash()));
            $this->info(sprintf('Bcrypt (cost = 16)  : %s', Bcrypt::createFromRawPassword(password: $rawPassword, cost: 16)->getFullHash()));

            $this->info(sprintf('Argon (default)     : %s', Argon::createFromRawPassword(password: $rawPassword)->getFullHash()));
            $this->info(sprintf('Argon (less memory) : %s', Argon::createFromRawPassword(password: $rawPassword, memoryCost: 3200)->getFullHash()));
            $this->info(sprintf('Argon (more memory) : %s', Argon::createFromRawPassword(password: $rawPassword, memoryCost: 256000, timeCost: 8, threads: 2)->getFullHash()));
        } catch (Throwable $throwable) {
            $this->error('Something went wrong calculating your hashes.');
            $this->error($throwable->getMessage());

            return 1;
        }

        $this->end();
        $this->comment(sprintf('Generated %d hashes in %dms.', 9, $this->runtime()));

        return 0;
    }
}