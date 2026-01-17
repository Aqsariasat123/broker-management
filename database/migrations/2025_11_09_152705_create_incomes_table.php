<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIncomesTable extends Migration
{
    public function up()
    {
        Schema::create('incomes', function (Blueprint $table) {
            $table->id();
            $table->string('income_code')->unique();
            $table->foreignId('commission_statement_id')->nullable()->constrained('commission_statements')->nullOnDelete();
            $table->foreignId('income_source_id')->nullable()->constrained('lookup_values')->nullOnDelete();
            $table->date('date_received')->nullable();
            $table->decimal('amount_received', 15, 2)->nullable();
            $table->string('description')->nullable();
            $table->foreignId('category_id')->nullable()->constrained('lookup_values')->nullOnDelete();
            $table->foreignId('mode_of_payment_id')->nullable()->constrained('lookup_values')->nullOnDelete();
            $table->string('bank_statement_path')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('incomes');
    }
}
