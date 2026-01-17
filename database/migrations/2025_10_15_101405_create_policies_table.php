<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('policies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->string('policy_no')->unique();
            $table->string('policy_code')->unique();
            $table->foreignId('insurer_id')->nullable()->constrained('lookup_values')->nullOnDelete();
            $table->foreignId('policy_class_id')->nullable()->constrained('lookup_values')->nullOnDelete();
            $table->foreignId('policy_plan_id')->nullable()->constrained('lookup_values')->nullOnDelete();
            $table->decimal('sum_insured', 15, 2)->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->string('insured')->nullable();
            $table->string('insured_item')->nullable();
            $table->foreignId('policy_status_id')->nullable()->constrained('lookup_values')->nullOnDelete();
            $table->date('date_registered');
            $table->boolean('renewable')->default(true);
            $table->foreignId('business_type_id')->nullable()->constrained('lookup_values')->nullOnDelete();
            $table->integer('term')->nullable();
            $table->string('term_unit')->nullable();
            $table->decimal('base_premium', 15, 2)->nullable();
            $table->decimal('premium', 15, 2)->nullable();
            $table->foreignId('frequency_id')->nullable()->constrained('lookup_values')->nullOnDelete();
            $table->foreignId('pay_plan_lookup_id')->nullable()->constrained('lookup_values')->nullOnDelete();
            $table->foreignId('agency_id')->nullable()->constrained('lookup_values')->nullOnDelete();
            $table->string('agent')->nullable();
            $table->foreignId('channel_id')->nullable()->constrained('lookup_values')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('policies');
    }
};