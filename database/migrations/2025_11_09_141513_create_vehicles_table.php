<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehiclesTable extends Migration
{
    public function up()
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('vehicle_code')->unique();
            $table->foreignId('policy_id')->constrained()->cascadeOnDelete();
            $table->string('regn_no')->nullable();
            $table->string('make')->nullable();
            $table->string('model')->nullable();
            $table->string('type')->nullable();
            $table->string('usage')->nullable();
            $table->year('manufacture_year')->nullable();
            $table->decimal('value', 15, 2)->nullable();
            $table->string('engine')->nullable();
            $table->string('engine_type')->nullable();
            $table->string('engine_capacity')->nullable();
            $table->string('engine_no')->nullable();
            $table->string('chassis_no')->nullable();
            $table->date('cover_from')->nullable();
            $table->date('cover_to')->nullable();
            $table->string('slta_certificate_path')->nullable();
            $table->string('proof_of_purchase_path')->nullable();
            $table->string('value_certificate_path')->nullable();
             $table->string('vehicle_seats')->nullable();
            $table->string('vehicle_color')->nullable();

            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('vehicles');
    }
}
