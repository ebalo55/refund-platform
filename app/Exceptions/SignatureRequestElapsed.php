<?php

namespace App\Exceptions;

use Throwable;

class SignatureRequestElapsed extends SafeException
{
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct(
            config("exceptions.errors.SIGNATURE_REQUEST_ELAPSED.message"),
            config("exceptions.errors.SIGNATURE_REQUEST_ELAPSED.code")
        );
    }
}
