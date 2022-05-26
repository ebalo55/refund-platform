<?php

namespace App\Interfaces;

use Throwable;

interface SafeException
{
    function __construct(string $message, int $code, ?Throwable $previous);
}
