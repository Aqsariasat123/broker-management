<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('contact_no')->nullable()->after('mobile_no');
            $table->string('home_no')->nullable()->after('contact_no');
            $table->string('pc_channel')->nullable()->after('source_name');
            $table->string('savings_budget')->nullable()->after('monthly_income');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn(['contact_no', 'home_no', 'pc_channel', 'savings_budget']);
        });
    }
};
