<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('claims', function (Blueprint $table) {
            // Add claim_stage column if it doesn't exist
            if (!Schema::hasColumn('claims', 'claim_stage')) {
                $table->string('claim_stage')->nullable()->after('claim_summary');
            }
            
            // Also ensure policy_id exists (in case the previous migration hasn't run)
            if (!Schema::hasColumn('claims', 'policy_id')) {
                $table->foreignId('policy_id')->nullable()->after('id')->constrained('policies')->nullOnDelete();
            }
            
            // Also ensure client_id exists
            if (!Schema::hasColumn('claims', 'client_id')) {
                $table->foreignId('client_id')->nullable()->after('policy_id')->constrained('clients')->nullOnDelete();
            }
        });
    }

    public function down()
    {
        Schema::table('claims', function (Blueprint $table) {
            if (Schema::hasColumn('claims', 'claim_stage')) {
                $table->dropColumn('claim_stage');
            }
        });
    }
};
