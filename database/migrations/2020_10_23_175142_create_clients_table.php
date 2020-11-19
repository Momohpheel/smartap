<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('email')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('token');
            $table->string('address')->nullable();
            $table->string('password');
            $table->string('long')->nullable();
            $table->string('lat')->nullable();
            $table->string('logo')->nullable();
            $table->string('state')->nullable();
            $table->string('lga')->nullable();
            $table->string('description')->nullable();
            //$table->string('subscription_plan')->nullable();
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
        Schema::dropIfExists('clients');
    }
}
