<?php

namespace Ypho\Hashing\Exceptions;

use Exception;

class BcryptException extends Exception
{
    /** @phpstan-ignore-next-line */
    protected $code = 500;
}