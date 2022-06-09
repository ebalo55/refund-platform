<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

class EmitPaymentJob implements ShouldQueue, ShouldBeUnique
{
	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

	/**
	 * The number of times the job may be attempted.
	 *
	 * @var int
	 */
	public int $tries = 5;

	/**
	 * Create a new job instance.
	 *
	 * @return void
	 */
	public
	function __construct(
		protected User $user
	) {}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public
	function handle(): void
	{
		if (!$this->user->refund_completed) {
			$json_data = json_encode(
				[
					"address" => $this->user->refund_wallet,
					"amount"  => $this->user->refund_amount,
				]
			);

			$node_finder = new ExecutableFinder();
			$node = $node_finder->find("node");

			// Runs the refund node process using symphony process
			$process = new Process([$node, __DIR__ . "/../../refund.js", $json_data, env("WALLET_PRIVATE_KEY")], __DIR__, timeout: null);
			$process->setOptions(['create_new_console' => true]);
			$process->run();
			if ($process->isSuccessful()) {
				$this->user->refund_completed = true;
				$this->user->save();
			}
			else {
				logger("EmitPayment - ERROR @ " . now(),
				       [
					       "command_line" => $process->getCommandLine(),
					       "stdout"       => $process->getOutput(),
					       "stderr"       => $process->getErrorOutput(),
				       ]
				);
				$this->fail();
			}
		}
	}

	/**
	 * Get the middleware the job should pass through.
	 *
	 * @return array
	 */
	public
	function middleware(): array
	{
		return [
			(new WithoutOverlapping($this->user->id))->expireAfter(300) // 5min
		];
	}

	/**
	 * The unique ID of the job.
	 *
	 * @return string
	 */
	public
	function uniqueId(): string
	{
		return "{$this->user->id}";
	}
}
