<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServiceInteractionsTable extends Migration
{
    public function up() {
        Schema::create('service_interactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('service');
            $table->json('request_body');
            $table->integer('response_code');
            $table->json('response_body');
            $table->string('ip_address');
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('service_interactions');
    }
}
