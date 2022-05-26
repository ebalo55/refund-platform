<?php

namespace App\Exceptions;

use Throwable;

class UnverifiableSignature extends SafeException
{
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct(
            config("exceptions.errors.UNVERIFIABLE_SIGNATURE.message"),
            config("exceptions.errors.UNVERIFIABLE_SIGNATURE.code")
        );
    }
}
