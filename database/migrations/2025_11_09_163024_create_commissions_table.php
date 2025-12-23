<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommissionsTable extends Migration
{
    public function up()
    {
        Schema::create('commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('policy_id')->constrained()->cascadeOnDelete();
            $table->string('grouping')->nullable();
            $table->decimal('basic_premium', 15, 2)->nullable();
            $table->decimal('rate', 8, 2)->nullable();
            $table->decimal('amount_due', 15, 2)->nullable();
            $table->foreignId('payment_status_id')->nullable()->constrained('lookup_values')->nullOnDelete();
            $table->decimal('amount_received', 15, 2)->nullable();
            $table->date('date_received')->nullable();
            $table->string('statement_no')->nullable();
            $table->foreignId('mode_of_payment_id')->nullable()->constrained('lookup_values')->nullOnDelete();
            $table->decimal('variance', 15, 2)->nullable();
            $table->string('variance_reason')->nullable();
            $table->date('date_due')->nullable();
            $table->string('commission_code')->unique();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('commissions');
    }
}
