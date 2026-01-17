<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::table('claims', function (Blueprint $table) {
            // Check if policy_id column exists
            if (!Schema::hasColumn('claims', 'policy_id')) {
                // Add policy_id as foreign key
                $table->foreignId('policy_id')->nullable()->after('id')->constrained('policies')->nullOnDelete();
            } else {
                // Check if it's already a foreign key
                $foreignKeys = DB::select("
                    SELECT CONSTRAINT_NAME 
                    FROM information_schema.KEY_COLUMN_USAGE 
                    WHERE TABLE_SCHEMA = DATABASE() 
                    AND TABLE_NAME = 'claims' 
                    AND COLUMN_NAME = 'policy_id'
                    AND REFERENCED_TABLE_NAME IS NOT NULL
                ");
                
                if (empty($foreignKeys)) {
                    // Add foreign key constraint if it doesn't exist
                    $table->foreign('policy_id')->references('id')->on('policies')->nullOnDelete();
                }
            }
        });

        // Migrate existing data: try to match policy_no to policy_id
        if (Schema::hasColumn('claims', 'policy_no') && Schema::hasColumn('claims', 'policy_id')) {
            DB::statement("
                UPDATE claims c
                INNER JOIN policies p ON c.policy_no = p.policy_no
                SET c.policy_id = p.id
                WHERE c.policy_id IS NULL
            ");
        }
    }

    public function down()
    {
        Schema::table('claims', function (Blueprint $table) {
            // Drop foreign key constraint
            $table->dropForeign(['policy_id']);
            
            // Optionally drop the column (uncomment if needed)
            // $table->dropColumn('policy_id');
        });
    }
};
