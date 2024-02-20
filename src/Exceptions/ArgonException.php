<?php

namespace Ypho\Hashing\Exceptions;

use Exception;

class ArgonException extends Exception
{
    /** @phpstan-ignore-next-line */
    protected $code = 500;
}