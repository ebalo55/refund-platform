<?php

namespace App\Exceptions;

use Throwable;

class UserAddressDoesNotMatch extends SafeException
{
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct(
            config("exceptions.errors.USER_ADDRESS_DOES_NOT_MATCH.message"),
            config("exceptions.errors.USER_ADDRESS_DOES_NOT_MATCH.code")
        );
    }
}
