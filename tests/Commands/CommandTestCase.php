<?php

namespace Ypho\Hashing\Commands;

use Illuminate\Console\Application;
use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use PHPUnit\Framework\TestCase;

class CommandTestCase extends TestCase
{
    protected Application $artisan;

    protected function setUp(): void
    {
        parent::setUp();

        $container = new Container();
        $events = new Dispatcher($container);
        $this->artisan = new Application($container, $events, 'v1');
    }
}