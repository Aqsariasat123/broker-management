<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClaimsTable extends Migration
{
    public function up()
    {
        Schema::create('claims', function (Blueprint $table) {
            $table->id();
            $table->string('claim_id')->unique();
            $table->foreignId('policy_id')->nullable()->constrained('policies')->nullOnDelete();
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->foreignId('vehicle_id')->nullable()->constrained('vehicles')->nullOnDelete();
            $table->date('loss_date')->nullable();
            $table->date('claim_date')->nullable();
            $table->decimal('claim_amount', 15, 2)->nullable();
            $table->text('claim_summary')->nullable();
            $table->string('claim_stage')->nullable();
            $table->string('status')->nullable();
            $table->date('close_date')->nullable();
            $table->decimal('paid_amount', 15, 2)->nullable();
            $table->text('settlment_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('claims');
    }
}
