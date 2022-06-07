<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
			$table->string('first_name')->nullable();
			$table->string('last_name')->nullable();
            $table->string('original_wallet')->unique();
            $table->string('refund_wallet');
            $table->string('refund_amount');
            $table->boolean('accepted_terms_and_conditions')->default(false);
            $table->boolean('accepted_waiver_of_liability')->default(false);
            $table->boolean('logged_in')->default(false);
			$table->boolean('refund_completed')->default(false);
            $table->json('persona_references')->default("[]");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
