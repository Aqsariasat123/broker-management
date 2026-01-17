<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatementsTable extends Migration
{
    public function up()
    {
        Schema::create('statements', function (Blueprint $table) {
            $table->id();
            $table->string('statement_no')->unique();
            $table->string('year')->nullable();
            $table->unsignedBigInteger('insurer_id')->nullable();
            $table->string('business_category')->nullable();
            $table->date('date_received')->nullable();
            $table->decimal('amount_received', 15, 2)->nullable();
            $table->unsignedBigInteger('mode_of_payment_id')->nullable();
            $table->string('remarks')->nullable();
            $table->timestamps();

            $table->foreign('insurer_id')->references('id')->on('lookup_values')->nullOnDelete();
            $table->foreign('mode_of_payment_id')->references('id')->on('lookup_values')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::dropIfExists('statements');
    }
}
