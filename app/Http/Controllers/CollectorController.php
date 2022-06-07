<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Response;
use Inertia\ResponseFactory;

class CollectorController extends Controller
{
	public
	function collectInformation(
		Request $request
	): \Symfony\Component\HttpFoundation\Response
	{
		$request->validate(
			[
				"first_name"           => "required|string|max:255",
				"last_name"            => "required|string|max:255",
				"terms_and_conditions" => "required|boolean|accepted",
				"liability"            => "required|boolean|accepted",
			]
		);

		/** @var \App\Models\User $user */
		$user = auth()->user();
		$user->accepted_terms_and_conditions = true;
		$user->accepted_waiver_of_liability = true;
		$user->first_name = $request->input("first_name");
		$user->last_name = $request->input("last_name");
		$user->save();

		return inertia()->location(
			"https://melodity.withpersona.com/verify?" .
			"inquiry-template-id=itmpl_qsPvbUsTWT1fv6wWPSM3pjXB&" .
			"environment=" . (config("app.env") === "production"
				? "production"
				: "sandbox") . "&" .
			"reference-id={$user->original_wallet}_" . now()->getTimestamp() . "&" .
			"redirect-uri=" . route("authenticated.get.updater.dashboard")
		);
	}

	public
	function renderCollectInformationPage(): Response|ResponseFactory
	{
		/** @var \App\Models\User $user */
		$user = auth()->user();
		$errors = null;

		if (session()->has("update_error")) {
			$errors = session("update_error", null);
		}
		return inertia("CollectInfo", [
			"first_name"    => $user->first_name,
			"last_name"     => $user->last_name,
			"update_errors" => $errors,
		]);
	}
}
