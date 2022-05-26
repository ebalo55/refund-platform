<?php

namespace App\Exceptions;

use Throwable;

class MissingSignatureVerificationData extends SafeException
{
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct(
            config("exceptions.errors.MISSING_SIGNATURE_VERIFICATION_DATA.message"),
            config("exceptions.errors.MISSING_SIGNATURE_VERIFICATION_DATA.code")
        );
    }
}
