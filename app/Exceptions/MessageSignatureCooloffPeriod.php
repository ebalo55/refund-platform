<?php

namespace App\Exceptions;

use App\Enums\ResponseCode;
use Throwable;

class MessageSignatureCooloffPeriod extends SafeException
{
    protected ResponseCode $response_code = ResponseCode::HTTP_FORBIDDEN;

    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct(
            config("exceptions.errors.MESSAGE_SIGNATURE_COOLOFF_PERIOD.message"),
            config("exceptions.errors.MESSAGE_SIGNATURE_COOLOFF_PERIOD.code")
        );
    }
}
