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
            // WEAK ALGORITHMS
            $this->comment('MD5 / SHA1 / SHA2 / SHA3');
            $this->start();
            $this->info(sprintf('MD5    : %s', hash('md5', $rawPassword)));
            $this->info(sprintf('SHA1   : %s', hash('sha1', $rawPassword)));
            $this->info(sprintf('SHA256 : %s', hash('sha256', $rawPassword)));
            $this->info(sprintf('SHA384 : %s', hash('sha384', $rawPassword)));
            $this->info(sprintf('SHA512 : %s', hash('sha512', $rawPassword)));
            $this->end();

            // BCRYPT
            $this->newLine();
            $this->comment('Bcrypt');

            $this->start();
            $hash = Bcrypt::createFromRawPassword(password: $rawPassword, cost: 4)->getFullHash();
            $this->end();
            $this->info(sprintf('%s %s ms: %s', str_pad('Bcrypt (cost = 4)', 24), str_pad($this->runtime(), 4), $hash));

            $this->start();
            $hash = Bcrypt::createFromRawPassword(password: $rawPassword)->getFullHash();
            $this->end();
            $this->info(sprintf('%s %s ms: %s', str_pad('Bcrypt (default, 10)', 24), str_pad($this->runtime(), 4), $hash));

            $this->start();
            $hash = Bcrypt::createFromRawPassword(password: $rawPassword, cost: 16)->getFullHash();
            $this->end();
            $this->info(sprintf('%s %s ms: %s', str_pad('Bcrypt (cost = 16)', 24), str_pad($this->runtime(), 4), $hash));

            // ARGON
            $this->newLine();
            $this->comment('Argon');

            $this->start();
            $hash = Argon::createFromRawPassword(password: $rawPassword)->getFullHash();
            $this->end();
            $this->info(sprintf('%s %sms: %s', str_pad('Argon (default)', 24), str_pad($this->runtime(), 4), $hash));

            $this->start();
            $hash = Argon::createFromRawPassword(password: $rawPassword, memoryCost: 3200)->getFullHash();
            $this->end();
            $this->info(sprintf('%s %sms: %s', str_pad('Argon (less memory)', 24), str_pad($this->runtime(), 4), $hash));

            $this->start();
            $hash = Argon::createFromRawPassword(password: $rawPassword, memoryCost: 256000)->getFullHash();
            $this->end();
            $this->info(sprintf('%s %sms: %s', str_pad('Argon (more memory)', 24), str_pad($this->runtime(), 4), $hash));
        } catch (Throwable $throwable) {
            $this->error('Something went wrong calculating your hashes.');
            $this->error($throwable->getMessage());

            return 1;
        }

        return 0;
    }
}