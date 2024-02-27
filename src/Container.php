<?php

namespace Ypho\Hashing;

class Container extends \Illuminate\Container\Container
{
    /**
     * For some reason this method has been removed from the illuminate/container package
     * somewhere between 10.14 and 10.45, not sure why yet. To have the custom Artisan
     * implementation working, we create our own Container, and add the missing method.
     *
     * Yes, I could've chosen for the Symfony implementation, but the Illuminate version
     * has some useful methods that I don't want to write myself (e.g. $this->warn() etc.).
     */
    public function runningUnitTests(): bool
    {
        return false;
    }
}