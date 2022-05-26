<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $content = file_get_contents(__DIR__ . "/wallets.csv");
        $lines = explode(PHP_EOL, $content);

        foreach ($lines as $line) {
            $line = trim($line);
            if(!empty($line)) {
                [$wallet, $amount] = explode(",", $line);

                $integer = $amount;
                $decimals = str_repeat("0", 18);
                if(str_contains($amount, ".")) {
                    [$integer, $decimals] = explode(".", $amount);
                    $decimals = str_pad($decimals, 18, "0");
                }

                User::firstOrCreate([
                    "original_wallet" => $wallet
                ], [
                    "original_wallet" => $wallet,
                    "refund_wallet" => $wallet,
                    "refund_amount" => "$integer.$decimals"
                ]);
            }
        }
    }
}
