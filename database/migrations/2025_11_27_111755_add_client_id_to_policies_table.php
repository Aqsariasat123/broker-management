<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('policies')) {
            // Check if column exists but foreign key is missing
            $hasColumn = Schema::hasColumn('policies', 'client_id');
            $hasForeignKey = $this->hasForeignKey('policies', 'policies_client_id_foreign');
            
            if ($hasColumn && !$hasForeignKey) {
                // First, make column nullable to allow NULL values
                Schema::table('policies', function (Blueprint $table) {
                    $table->unsignedBigInteger('client_id')->nullable()->change();
                });
                
                // Set client_id to NULL for policies where client_id is 0 or invalid
                DB::table('policies')->where('client_id', 0)->orWhere('client_id', '')->update(['client_id' => null]);
                
                // Set client_id to NULL for policies where client_id doesn't exist in clients table
                DB::statement('UPDATE policies p 
                    LEFT JOIN clients c ON p.client_id = c.id 
                    SET p.client_id = NULL 
                    WHERE p.client_id IS NOT NULL AND c.id IS NULL');
                
                // Add foreign key constraint (nullable, so NULL values are allowed)
                Schema::table('policies', function (Blueprint $table) {
                    $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
                });
            } elseif (!$hasColumn) {
                // Column doesn't exist, add it as nullable
                Schema::table('policies', function (Blueprint $table) {
                    $table->foreignId('client_id')->nullable()->after('id')->constrained('clients')->nullOnDelete();
                });
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('policies') && Schema::hasColumn('policies', 'client_id')) {
            Schema::table('policies', function (Blueprint $table) {
                if ($this->hasForeignKey('policies', 'policies_client_id_foreign')) {
                    $table->dropForeign(['client_id']);
                }
            });
        }
    }
    
    private function hasForeignKey(string $table, string $constraintName): bool
    {
        $foreignKeys = DB::select(
            "SELECT CONSTRAINT_NAME 
             FROM information_schema.KEY_COLUMN_USAGE 
             WHERE TABLE_SCHEMA = DATABASE() 
             AND TABLE_NAME = ? 
             AND CONSTRAINT_NAME = ?",
            [$table, $constraintName]
        );
        
        return count($foreignKeys) > 0;
    }
};
