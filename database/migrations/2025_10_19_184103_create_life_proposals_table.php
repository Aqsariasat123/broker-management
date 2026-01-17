<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLifeProposalsTable extends Migration
{
    public function up()
    {
        Schema::create('life_proposals', function (Blueprint $table) {
            $table->id();

            $table->string('proposers_name')->nullable();

            $table->foreignId('contact_id')->constrained()->cascadeOnDelete();

            $table->foreignId('insurer_id')->nullable()
                ->constrained('lookup_values')->nullOnDelete();

            $table->foreignId('policy_plan_id')->nullable()
                ->constrained('lookup_values')->nullOnDelete();
            $table->foreignId('salutation_id')->nullable()
                ->constrained('lookup_values')->nullOnDelete();
            $table->decimal('sum_assured', 15, 2)->nullable();
            $table->integer('term')->nullable();
            $table->string('add_ons')->nullable();

            $table->date('offer_date')->nullable();
            $table->decimal('premium', 15, 2)->nullable();

            $table->foreignId('frequency_id')->nullable()
                ->constrained('lookup_values')->nullOnDelete();

            $table->foreignId('proposal_stage_id')->nullable()
                ->constrained('lookup_values')->nullOnDelete();

            $table->integer('age')->nullable();

            $table->foreignId('status_id')->nullable()
                ->constrained('lookup_values')->nullOnDelete();

            $table->foreignId('source_of_payment_id')->nullable()
                ->constrained('lookup_values')->nullOnDelete();

            $table->string('mcr')->nullable();
            $table->string('agency')->nullable();

            $table->string('prid')->unique();

            $table->foreignId('class_id')->nullable()
                ->constrained('lookup_values')->nullOnDelete();

            $table->boolean('is_submitted')->default(false);

            $table->string('sex', 1)->nullable();
            $table->integer('anb')->nullable();

            /* ---------- Riders ---------- */
            $table->json('riders')->nullable();
            $table->json('rider_premiums')->nullable();

            /* ---------- Premium Breakdown ---------- */
            $table->decimal('annual_premium', 15, 2)->nullable();
            $table->decimal('base_premium', 15, 2)->nullable();
            $table->decimal('admin_fee', 15, 2)->nullable();
            $table->decimal('total_premium', 15, 2)->nullable();

            /* ---------- Medical Flag ---------- */
            $table->boolean('medical_examination_required')->default(false);

            /* ---------- Policy Details ---------- */
            $table->string('policy_no')->nullable();
            $table->decimal('loading_premium', 15, 2)->nullable();
            $table->date('start_date')->nullable();
            $table->date('maturity_date')->nullable();

            /* ---------- Payment ---------- */
            $table->string('method_of_payment')->nullable();

            /* ---------- Source ---------- */
            $table->string('source_name')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('life_proposals');
    }
}
