<?php

namespace App\Http\Controllers;

use App\Exceptions\SafeException;
use App\Exceptions\UserAddressDoesNotMatch;
use App\Models\User;
use Doinc\PersonaKyc\Persona;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Str;
use Inertia\Response;
use Inertia\ResponseFactory;

class UpdaterController extends Controller
{
    /**
     * Displays the update dashboard to the user if coming back from Persona's KYC
     * @throws SafeException
     */
    public function displayDashboard(Request $request): Response|ResponseFactory|Application|RedirectResponse|Redirector
    {
		// fields[name-last][type]=string&fields[name-last][value]=SAMPLE&fields[name-first][type]=string&fields[name-first][value]=ALEXANDER J&fields[name-middle][type]=string&fields[name-middle][value]=
        $request->validate([
            "inquiry-id" => "required|string|regex:/^inq_[A-Za-z0-9]{24}$/",
            "status" => "required|string|in:completed",
            "reference-id" => "required|string",
            "fields.name-last.value" => "required|string|min:1",
            "fields.name-first.value" => "required|string|min:1",
        ]);

		/** @var User $user */
		$user = auth()->user();
		if(Str::lower($user->first_name) !== Str::lower($request->input("fields.name-first.value")) ||
			Str::lower($user->last_name) !== Str::lower($request->input("fields.name-last.value"))) {
			session()->flash("update_error", "First or last name does not match the one provided in your ID");
			return redirect()->route("authenticated.get.collector.identify");
		}

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
                    "refund_amount" => floatval($user->refund_amount) * 0.4,
                    "original_address" => $user->original_wallet
                ]);
            }

            throw new UserAddressDoesNotMatch();
        }
        catch (Exception $exception) {
            throw new SafeException($exception->getMessage(), $exception->getCode());
        }
    }

    public function updateRefundAddress(Request $request): RedirectResponse
    {
        $request->validate([
            "refund_address" => "required|string|regex:/^0x[a-fA-F0-9]{40}$/",
        ]);

        /** @var User $user */
        $user = auth()->user();

        $user->refund_wallet = $request->input("refund_address");
        $user->save();

        return back();
    }
}
