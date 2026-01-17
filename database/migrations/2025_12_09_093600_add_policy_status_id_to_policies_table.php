<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('policies')) {
            return;
        }

        // Helper function to migrate varchar to foreign key
        $migrateColumn = function($varcharColumn, $foreignKeyColumn, $categoryName, $afterColumn = null) {
            if (!Schema::hasColumn('policies', $foreignKeyColumn)) {
                // Step 1: Add the foreign key column
                Schema::table('policies', function (Blueprint $table) use ($foreignKeyColumn, $afterColumn) {
                    if ($afterColumn && Schema::hasColumn('policies', $afterColumn)) {
                        $table->unsignedBigInteger($foreignKeyColumn)->nullable()->after($afterColumn);
                    } else {
                        $table->unsignedBigInteger($foreignKeyColumn)->nullable();
                    }
                });
                
                // Step 2: Migrate data from old varchar column if it exists
                if (Schema::hasColumn('policies', $varcharColumn)) {
                    try {
                        $policies = DB::table('policies')->whereNotNull($varcharColumn)->get();
                        
                        foreach ($policies as $policy) {
                            $lookupValue = DB::table('lookup_values')
                                ->join('lookup_categories', 'lookup_values.lookup_category_id', '=', 'lookup_categories.id')
                                ->where('lookup_categories.name', $categoryName)
                                ->where('lookup_values.name', $policy->{$varcharColumn})
                                ->select('lookup_values.id')
                                ->first();
                            
                            if ($lookupValue) {
                                DB::table('policies')
                                    ->where('id', $policy->id)
                                    ->update([$foreignKeyColumn => $lookupValue->id]);
                            }
                        }
                    } catch (\Exception $e) {
                        \Log::warning("Failed to migrate data for {$varcharColumn}: " . $e->getMessage());
                    }
                }
                
                // Step 3: Add foreign key constraint
                Schema::table('policies', function (Blueprint $table) use ($foreignKeyColumn) {
                    try {
                        $table->foreign($foreignKeyColumn)
                              ->references('id')
                              ->on('lookup_values')
                              ->nullOnDelete();
                    } catch (\Exception $e) {
                        \Log::warning("Failed to add foreign key for {$foreignKeyColumn}: " . $e->getMessage());
                    }
                });
            }
        };

        // Add all missing foreign key columns
        $migrateColumn('insurer', 'insurer_id', 'Insurer', 'client_id');
        $migrateColumn('policy_class', 'policy_class_id', 'Policy Classes', 'insurer_id');
        $migrateColumn('policy_plan', 'policy_plan_id', 'Policy Plans', 'policy_class_id');
        $migrateColumn('policy_status', 'policy_status_id', 'Policy Status', 'insured');
        $migrateColumn('biz_type', 'business_type_id', 'Business Type', 'renewable');
        $migrateColumn('frequency', 'frequency_id', 'Frequency', 'premium');
        $migrateColumn('pay_plan', 'pay_plan_lookup_id', 'Payment Plan', 'frequency_id');
        $migrateColumn('agency', 'agency_id', 'APL Agency', 'pay_plan_lookup_id');
        
        // Add channel_id (no old column to migrate from)
        if (!Schema::hasColumn('policies', 'channel_id')) {
            Schema::table('policies', function (Blueprint $table) {
                if (Schema::hasColumn('policies', 'agent')) {
                    $table->unsignedBigInteger('channel_id')->nullable()->after('agent');
                } else {
                    $table->unsignedBigInteger('channel_id')->nullable();
                }
            });
            
            Schema::table('policies', function (Blueprint $table) {
                try {
                    $table->foreign('channel_id')
                          ->references('id')
                          ->on('lookup_values')
                          ->nullOnDelete();
                } catch (\Exception $e) {
                    \Log::warning("Failed to add foreign key for channel_id: " . $e->getMessage());
                }
            });
        }
        
        // Add policy_code if it doesn't exist
        if (!Schema::hasColumn('policies', 'policy_code')) {
            Schema::table('policies', function (Blueprint $table) {
                $table->string('policy_code', 50)->nullable()->after('policy_no');
            });
            
            // Generate policy_code from existing data
            try {
                $policies = DB::table('policies')->whereNull('policy_code')->get();
                foreach ($policies as $policy) {
                    // Check if policy_id column exists (old column name)
                    $policyCode = null;
                    if (Schema::hasColumn('policies', 'policy_id') && !empty($policy->policy_id)) {
                        $policyCode = $policy->policy_id;
                    } else {
                        $policyCode = 'POL' . str_pad($policy->id, 6, '0', STR_PAD_LEFT);
                    }
                    
                    DB::table('policies')
                        ->where('id', $policy->id)
                        ->update(['policy_code' => $policyCode]);
                }
                
                // Now make it unique
                Schema::table('policies', function (Blueprint $table) {
                    $table->unique('policy_code');
                });
            } catch (\Exception $e) {
                \Log::warning("Failed to generate policy codes: " . $e->getMessage());
            }
        }
        
        // Optional: Drop old varchar columns (commented out for safety)
        // Uncomment these after verifying data migration was successful
        /*
        $oldColumns = ['insurer', 'policy_class', 'policy_plan', 'policy_status', 
                       'biz_type', 'frequency', 'pay_plan', 'agency'];
        Schema::table('policies', function (Blueprint $table) use ($oldColumns) {
            foreach ($oldColumns as $column) {
                if (Schema::hasColumn('policies', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
        */
    }

    public function down(): void
    {
        if (!Schema::hasTable('policies')) {
            return;
        }

        $foreignKeyColumns = [
            'channel_id',
            'agency_id',
            'pay_plan_lookup_id',
            'frequency_id',
            'business_type_id',
            'policy_status_id',
            'policy_plan_id',
            'policy_class_id',
            'insurer_id',
        ];
        
        // Drop foreign keys first
        foreach ($foreignKeyColumns as $column) {
            if (Schema::hasColumn('policies', $column)) {
                Schema::table('policies', function (Blueprint $table) use ($column) {
                    // Try to drop the foreign key
                    try {
                        // Get all foreign keys for this table
                        $foreignKeys = DB::select("
                            SELECT CONSTRAINT_NAME 
                            FROM information_schema.KEY_COLUMN_USAGE 
                            WHERE TABLE_SCHEMA = DATABASE() 
                            AND TABLE_NAME = 'policies' 
                            AND COLUMN_NAME = ?
                            AND REFERENCED_TABLE_NAME IS NOT NULL
                        ", [$column]);
                        
                        foreach ($foreignKeys as $fk) {
                            $table->dropForeign($fk->CONSTRAINT_NAME);
                        }
                    } catch (\Exception $e) {
                        \Log::warning("Failed to drop foreign key for {$column}: " . $e->getMessage());
                    }
                });
            }
        }
        
        // Drop columns
        Schema::table('policies', function (Blueprint $table) use ($foreignKeyColumns) {
            foreach ($foreignKeyColumns as $column) {
                if (Schema::hasColumn('policies', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
        
        // Drop policy_code
        if (Schema::hasColumn('policies', 'policy_code')) {
            Schema::table('policies', function (Blueprint $table) {
                try {
                    $table->dropUnique(['policy_code']);
                } catch (\Exception $e) {
                    // Unique constraint might not exist
                }
                $table->dropColumn('policy_code');
            });
        }
    }
};