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
        Schema::table('nominees', function (Blueprint $table) {
            // First, drop the foreign key constraint if it exists
            $table->dropForeign(['policy_id']);
        });
        
        Schema::table('nominees', function (Blueprint $table) {
            // Modify the column to be nullable
            $table->unsignedBigInteger('policy_id')->nullable()->change();
        });
        
        Schema::table('nominees', function (Blueprint $table) {
            // Re-add the foreign key constraint (with onDelete cascade or set null)
            $table->foreign('policy_id')
                  ->references('id')
                  ->on('policies')
                  ->onDelete('set null'); // When policy is deleted, set nominee's policy_id to null
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nominees', function (Blueprint $table) {
            // Drop the foreign key
            $table->dropForeign(['policy_id']);
        });
        
        Schema::table('nominees', function (Blueprint $table) {
            // Change back to not nullable
            $table->unsignedBigInteger('policy_id')->nullable(false)->change();
        });
        
        Schema::table('nominees', function (Blueprint $table) {
            // Re-add the foreign key constraint
            $table->foreign('policy_id')
                  ->references('id')
                  ->on('policies')
                  ->onDelete('cascade'); // Or whatever your original constraint was
        });
    }
};