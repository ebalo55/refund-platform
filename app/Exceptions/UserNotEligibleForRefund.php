<?php

namespace App\Exceptions;

use Throwable;

class UserNotEligibleForRefund extends SafeException
{
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct(
            config("exceptions.errors.USER_NOT_ELIGIBLE_FOR_REFUND.message"),
            config("exceptions.errors.USER_NOT_ELIGIBLE_FOR_REFUND.code")
        );
    }
}
