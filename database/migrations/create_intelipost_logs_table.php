<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('intelipost_logs', function (Blueprint $table) {
            $table->id();
            $table->string('endpoint');
            $table->json('request_data');
            $table->json('response_data');
            $table->integer('status_code');
            $table->float('execution_time');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('intelipost_logs');
    }
};
