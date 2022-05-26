<?php
return [
    /*
     * |--------------------------------------------------------------------------
     * | Wallet related errors
     * |--------------------------------------------------------------------------
     * |
     * | Defines the wallet error messages and codes.
     * | NOTE: Each of the code should be unique for a quick and easier
     * |       identification of flaws
     * |
     */
    "errors" => [
        "MESSAGE_SIGNATURE_COOLOFF_PERIOD" => [
            "message" => "Cannot request a new message, wait a few seconds and try again",
            "code" => 1100
        ],
        "UNVERIFIABLE_SIGNATURE" => [
            "message" => "Unverifiable signature provided or signature not matching provided address",
            "code" => 1101
        ],
        "MISSING_SIGNATURE_VERIFICATION_DATA" => [
            "message" => "Missing signature data",
            "code" => 1102
        ],
        "SIGNATURE_REQUEST_ELAPSED" => [
            "message" => "Signature request elapsed, please try again or check your network connectivity",
            "code" => 1103
        ],
        "USER_NOT_ELIGIBLE_FOR_REFUND" => [
            "message" => "The provided wallet is not eligible for refund try a different one",
            "code" => 1104
        ],
        "USER_ADDRESS_DOES_NOT_MATCH" => [
            "message" => "Unmatching wallet address found, something may went wrong",
            "code" => 1105
        ],
    ]
];
