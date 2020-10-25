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
            $table->string('token', 80)->unique()->nullable()->default(null);
            $table->string('address');
            $table->string('long');
            $table->string('lat');
            $table->string('url');
            $table->string('logo');
            $table->string('state');
            $table->string('lga');
            $table->string('description');
            $table->string('subscription_plan');

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
