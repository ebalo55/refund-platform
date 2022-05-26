<?php

namespace App\Http\Controllers;

use App\Exceptions\SafeException;
use App\Exceptions\UserAddressDoesNotMatch;
use App\Models\User;
use Doinc\PersonaKyc\Persona;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Response;
use Inertia\ResponseFactory;

class UpdaterController extends Controller
{
    /**
     * Displays the update dashboard to the user if coming back from Persona's KYC
     * @throws SafeException
     */
    public function displayDashboard(Request $request): Response|ResponseFactory
    {
        $request->validate([
            "inquiry-id" => "required|string|regex:/^inq_[A-Za-z0-9]{24}$/",
            "status" => "required|string|in:completed",
            "reference-id" => "required|string"
        ]);

        try {
            // retrieve the inquiry from persona's dashboard
            $inquiry = Persona::init()->inquiries()->get($request->input("inquiry-id"));
            [$address, ] = explode("_", $inquiry->reference_id);

            /** @var User $user */
            $user = auth()->user();

            if($user->original_wallet === $address && $inquiry->reference_id === $request->input("reference-id")) {
                $inquiries = collect(json_decode($user->persona_references));

                // the inquiry is not yet appended to the array, add it
                if(!$inquiries->contains($inquiry->id)) {
                    $inquiries->push($inquiry->id);
                }

                // save to the database the updated array of inquiries associated with the user
                $user->persona_references = json_encode($inquiries->toArray());
                $user->save();

                return inertia("Dashboard", [
                    "refund_address" => $user->refund_wallet,
                    "refund_amount" => floatval($user->refund_amount) * 0.3,
                    "original_address" => $user->original_wallet
                ]);
            }

            throw new UserAddressDoesNotMatch();
        }
        catch (Exception $exception) {
            throw new SafeException($exception->getMessage(), $exception->getCode());
        }
    }

    public function updateRefundAddress(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            "refund_address" => "required|string|regex:/^0x[a-fA-F0-9]{40}$/",
            "terms_and_conditions" => "required|boolean|accepted"
        ]);

        /** @var User $user */
        $user = auth()->user();

        $user->refund_wallet = $request->input("refund_address");
        $user->accepted_terms_and_conditions = $request->input("terms_and_conditions");
        $user->save();

        return back();
    }
}
