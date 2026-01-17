<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExpensesTable extends Migration
{
    public function up()
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('expense_code')->unique();
            $table->string('payee');
            $table->date('date_paid')->nullable();
            $table->decimal('amount_paid', 15, 2)->nullable();
            $table->string('description')->nullable();
            $table->foreignId('category_id')->nullable()->constrained('lookup_values')->nullOnDelete();
            $table->foreignId('mode_of_payment_id')->nullable()->constrained('lookup_values')->nullOnDelete();
            $table->string('attachment_path')->nullable();
            $table->string('receipt_path')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('expenses');
    }
}
