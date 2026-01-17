<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\QueryException;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $seeders = [
            \Database\Seeders\UserSeeder::class,
            \Database\Seeders\LookupTableSeeder::class,
            \Database\Seeders\LookupDataSeeder::class,
            \Database\Seeders\PolicySeeder::class,
            \Database\Seeders\TaskSeeder::class,
        ];

        foreach ($seeders as $seeder) {
            try {
                $this->call($seeder);
                $this->command->info("Seeded: {$seeder}");
            } catch (QueryException $e) {
                // SQLSTATE 23000 => integrity constraint violation (duplicate key)
                $sqlState = $e->getCode();
                $msg = $e->getMessage();
                if ($sqlState == '23000' || str_contains($msg, 'Integrity constraint violation') || str_contains(strtolower($msg), 'duplicate')) {
                    $this->command->warn("Skipping {$seeder} due to duplicate entry (unique constraint).");
                    continue;
                }
                // rethrow other DB exceptions
                throw $e;
            } catch (\Throwable $t) {
                // If any other unexpected error occurs, show and continue or rethrow based on preference
                $this->command->error("Error running {$seeder}: " . $t->getMessage());
                throw $t;
            }
        }
    }
}
