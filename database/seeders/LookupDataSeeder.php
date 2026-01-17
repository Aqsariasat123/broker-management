<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LookupDataSeeder extends Seeder
{
    public function run()
    {
        // Insert lookup categories
        $categories = [
            ['name' => 'Insurers', 'active' => 1],
            ['name' => 'Policy Classes', 'active' => 1],
            ['name' => 'Policy Plans', 'active' => 1],
            ['name' => 'Policy Statuses', 'active' => 1],
            ['name' => 'Business Types', 'active' => 1],
            ['name' => 'Term Units', 'active' => 1],
            ['name' => 'Frequencies', 'active' => 1],
            ['name' => 'Pay Plans', 'active' => 1],
            ['name' => 'Endorsements', 'active' => 1], // <-- New category
        ];

        foreach ($categories as $category) {
            $categoryId = DB::table('lookup_categories')->insertGetId($category);
            
            // Insert values based on category
            switch ($category['name']) {
                case 'Insurers':
                    $this->insertInsurers($categoryId);
                    break;
                case 'Policy Classes':
                    $this->insertPolicyClasses($categoryId);
                    break;
                case 'Policy Plans':
                    $this->insertPolicyPlans($categoryId);
                    break;
                case 'Policy Statuses':
                    $this->insertPolicyStatuses($categoryId);
                    break;
                case 'Business Types':
                    $this->insertBusinessTypes($categoryId);
                    break;
                case 'Term Units':
                    $this->insertTermUnits($categoryId);
                    break;
                case 'Frequencies':
                    $this->insertFrequencies($categoryId);
                    break;
                case 'Pay Plans':
                    $this->insertPayPlans($categoryId);
                    break;
                 case 'Endorsements':
                    $this->insertEndorsements($categoryId); // <-- New method
                    break;
            }
        }

        $this->command->info('Lookup data seeded successfully.');
    }

    private function insertEndorsements($categoryId)
    {
        $endorsements = [
            ['seq' => 1, 'name' => 'Change of Insured', 'active' => 1],
            ['seq' => 2, 'name' => 'Increase in Sum Insured', 'active' => 1],
            ['seq' => 3, 'name' => 'Decrease in Sum Insured', 'active' => 1],
            ['seq' => 4, 'name' => 'Change of Vehicle', 'active' => 1],
            ['seq' => 5, 'name' => 'Addition of Driver', 'active' => 1],
            ['seq' => 6, 'name' => 'Policy Extension', 'active' => 1],
        ];

        foreach ($endorsements as $endorsement) {
            DB::table('lookup_values')->insert([
                'lookup_category_id' => $categoryId,
                'seq' => $endorsement['seq'],
                'name' => $endorsement['name'],
                'active' => $endorsement['active'],
            ]);
        }
    }

    private function insertInsurers($categoryId)
    {
        $insurers = [
            ['seq' => 1, 'name' => 'SACOS', 'active' => 1],
            ['seq' => 2, 'name' => 'Alliance', 'active' => 1],
            ['seq' => 3, 'name' => 'Hsavy', 'active' => 1],
            ['seq' => 4, 'name' => 'AON', 'active' => 1],
            ['seq' => 5, 'name' => 'Marsh', 'active' => 1],
        ];

        foreach ($insurers as $insurer) {
            DB::table('lookup_values')->insert([
                'lookup_category_id' => $categoryId,
                'seq' => $insurer['seq'],
                'name' => $insurer['name'],
                'active' => $insurer['active'],
            ]);
        }
    }


    private function insertPolicyClasses($categoryId)
    {
        $classes = [
            ['seq' => 1, 'name' => 'Motor', 'active' => 1],
            ['seq' => 2, 'name' => 'General', 'active' => 1],
            ['seq' => 3, 'name' => 'Travel', 'active' => 1],
            ['seq' => 4, 'name' => 'Marine', 'active' => 1],
            ['seq' => 5, 'name' => 'Health', 'active' => 1],
            ['seq' => 6, 'name' => 'Life', 'active' => 1],
        ];

        foreach ($classes as $class) {
            DB::table('lookup_values')->insert([
                'lookup_category_id' => $categoryId,
                'seq' => $class['seq'],
                'name' => $class['name'],
                'active' => $class['active'],
            ]);
        }
    }

    private function insertPolicyPlans($categoryId)
    {
        $plans = [
            ['seq' => 1, 'name' => 'Comprehensive', 'active' => 1],
            ['seq' => 2, 'name' => 'Third Party', 'active' => 1],
            ['seq' => 3, 'name' => 'Householder\'s', 'active' => 1],
            ['seq' => 4, 'name' => 'Public Liability', 'active' => 1],
            ['seq' => 5, 'name' => 'Employer\'s Liability', 'active' => 1],
            ['seq' => 6, 'name' => 'Fire & Special Perils', 'active' => 1],
            ['seq' => 7, 'name' => 'House Insurance', 'active' => 1],
            ['seq' => 8, 'name' => 'Fire Industrial', 'active' => 1],
            ['seq' => 9, 'name' => 'World Wide Basic', 'active' => 1],
            ['seq' => 10, 'name' => 'Marine Hull', 'active' => 1],
        ];

        foreach ($plans as $plan) {
            DB::table('lookup_values')->insert([
                'lookup_category_id' => $categoryId,
                'seq' => $plan['seq'],
                'name' => $plan['name'],
                'active' => $plan['active'],
            ]);
        }
    }

    private function insertPolicyStatuses($categoryId)
    {
        $statuses = [
            ['seq' => 1, 'name' => 'In Force', 'active' => 1],
            ['seq' => 2, 'name' => 'DFR', 'active' => 1],
            ['seq' => 3, 'name' => 'Expired', 'active' => 1],
            ['seq' => 4, 'name' => 'Cancelled', 'active' => 1],
        ];

        foreach ($statuses as $status) {
            DB::table('lookup_values')->insert([
                'lookup_category_id' => $categoryId,
                'seq' => $status['seq'],
                'name' => $status['name'],
                'active' => $status['active'],
            ]);
        }
    }

    private function insertBusinessTypes($categoryId)
    {
        $types = [
            ['seq' => 1, 'name' => 'Direct', 'active' => 1],
            ['seq' => 2, 'name' => 'Transfer', 'active' => 1],
            ['seq' => 3, 'name' => 'Renewal', 'active' => 1],
        ];

        foreach ($types as $type) {
            DB::table('lookup_values')->insert([
                'lookup_category_id' => $categoryId,
                'seq' => $type['seq'],
                'name' => $type['name'],
                'active' => $type['active'],
            ]);
        }
    }

    private function insertTermUnits($categoryId)
    {
        $units = [
            ['seq' => 1, 'name' => 'Year', 'active' => 1],
            ['seq' => 2, 'name' => 'Month', 'active' => 1],
            ['seq' => 3, 'name' => 'Days', 'active' => 1],
        ];

        foreach ($units as $unit) {
            DB::table('lookup_values')->insert([
                'lookup_category_id' => $categoryId,
                'seq' => $unit['seq'],
                'name' => $unit['name'],
                'active' => $unit['active'],
            ]);
        }
    }

    private function insertFrequencies($categoryId)
    {
        $frequencies = [
            ['seq' => 1, 'name' => 'Annually', 'active' => 1],
            ['seq' => 2, 'name' => 'Monthly', 'active' => 1],
            ['seq' => 3, 'name' => 'Quarterly', 'active' => 1],
            ['seq' => 4, 'name' => 'One Off', 'active' => 1],
            ['seq' => 5, 'name' => 'Single', 'active' => 1],
        ];

        foreach ($frequencies as $frequency) {
            DB::table('lookup_values')->insert([
                'lookup_category_id' => $categoryId,
                'seq' => $frequency['seq'],
                'name' => $frequency['name'],
                'active' => $frequency['active'],
            ]);
        }
    }

    private function insertPayPlans($categoryId)
    {
        $plans = [
            ['seq' => 1, 'name' => 'Full', 'active' => 1],
            ['seq' => 2, 'name' => 'Instalments', 'active' => 1],
            ['seq' => 3, 'name' => 'Regular', 'active' => 1],
        ];

        foreach ($plans as $plan) {
            DB::table('lookup_values')->insert([
                'lookup_category_id' => $categoryId,
                'seq' => $plan['seq'],
                'name' => $plan['name'],
                'active' => $plan['active'],
            ]);
        }
    }
}