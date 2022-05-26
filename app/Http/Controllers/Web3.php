<?php

namespace App\Http\Controllers;

use App\Exceptions\MessageSignatureCooloffPeriod;
use App\Exceptions\MissingSignatureVerificationData;
use App\Exceptions\SignatureRequestElapsed;
use App\Exceptions\UnverifiableSignature;
use App\Exceptions\UserNotEligibleForRefund;
use App\Models\User;
use Elliptic\EC;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Str;
use kornrunner\Keccak;
use Throwable;

class Web3 extends Controller
{
    public const SESSION_SIGN_MESSAGE_KEY = "sign_message";

    protected string $remaining_seconds;

    protected function canRequestNewMessage(): bool
    {
        [, $ttl] = explode(".", session(self::SESSION_SIGN_MESSAGE_KEY));
        $this->remaining_seconds = (string)($ttl - (now()->timestamp - 60));
        return (int)$this->remaining_seconds <= 0;
    }

    /**
     * Create, save and return a message.
     * This message must be signed using the user's wallet in order to confirm the ownership of an address
     *
     * @return JsonResponse
     * @throws MessageSignatureCooloffPeriod
     */
    public function message(): JsonResponse
    {
        if (
            session()->has(self::SESSION_SIGN_MESSAGE_KEY) &&
            !$this->canRequestNewMessage()
        ) {
            throw new MessageSignatureCooloffPeriod($this->remaining_seconds);
        }

        $nonce = Str::random(32);
        $message = "Sign this message to confirm you own this wallet address.\n" .
            "This action will not cost any gas fees.\n\n" .
            "Nonce: " . $nonce;

        session()->put(
            self::SESSION_SIGN_MESSAGE_KEY,
            bin2hex($message) . "." . now()->timestamp
        );

        return $this->jsonResponse([
            "message" => $message
        ]);
    }

    /**
     * Verify that a wallet address correspond to the signature provided, redirect if the check passes
     *
     * @param Request $request
     * @return JsonResponse|Redirector|RedirectResponse|Application
     * @throws UnverifiableSignature
     * @throws MissingSignatureVerificationData
     * @throws SignatureRequestElapsed
     * @throws Throwable
     */
    public function verify(Request $request): JsonResponse|Redirector|RedirectResponse|Application
    {
        $request->validate([
            "signature" => "required|string",
            "address" => "required|string|size:42|regex:/0x[0-9A-Fa-f]{40}/"
        ]);

        if (!session()->has(self::SESSION_SIGN_MESSAGE_KEY)) {
            throw new MissingSignatureVerificationData();
        }

        // if the user can request a new message means that the current message time to live is elapsed so the message
        // is considered unverifiable
        if ($this->canRequestNewMessage()) {
            throw new SignatureRequestElapsed();
        }

        $address = $request->input('address');
        [$message,] = explode(".", session(self::SESSION_SIGN_MESSAGE_KEY));

        $is_verification_successful = $this->verifySignature(
            hex2bin($message),
            $request->input('signature'),
            $address
        );

        if ($is_verification_successful) {
            $user = User::whereOriginalWallet($address)->first();

            // if user is not registered create a simple on the fly registration and log in immediately
            if (is_null($user)) {
                throw new UserNotEligibleForRefund();
            }

            // if user exists login as the user (no email nor password required)
            auth()->login($user);

            $user->logged_in = true;
            $user->save();

            return $this->jsonResponse([
                "eligible" => true,
                "kyc_url" => "https://melodity.withpersona.com/verify?" .
                    "inquiry-template-id=itmpl_qsPvbUsTWT1fv6wWPSM3pjXB&" .
                    "environment=" . (config("app.env") === "production" ? "production" : "sandbox") . "&" .
                    "reference-id={$address}_" . now()->getTimestamp() . "&" .
                    "redirect-uri=" . route("authenticated.get.updater.dashboard")
            ]);
        }

        throw new UnverifiableSignature();
    }

    /**
     * Verify a message signature using an eth compatible address
     *
     * @param string $message
     * @param string $signature
     * @param string $address
     * @return bool
     */
    protected function verifySignature(string $message, string $signature, string $address): bool
    {
        try {
            $hash = Keccak::hash(sprintf("\x19Ethereum Signed Message:\n%s%s", strlen($message), $message), 256);
            $sign = [
                'r' => substr($signature, 2, 64),
                's' => substr($signature, 66, 64),
            ];
            $recid = ord(hex2bin(substr($signature, 130, 2))) - 27;

            if ($recid != ($recid & 1)) {
                return false;
            }

            $pubkey = (new EC('secp256k1'))->recoverPubKey($hash, $sign, $recid);
            $derived_address = '0x' . substr(Keccak::hash(substr(hex2bin($pubkey->encode('hex')), 1), 256), 24);

            return (Str::lower($address) === $derived_address);
        } catch (Throwable) {
            return false;
        }
    }
}
