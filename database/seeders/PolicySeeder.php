<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Policy;
use App\Models\Client;
use Carbon\Carbon;

class PolicySeeder extends Seeder
{
    public function run()
    {
        $policies = [
            [
                'policy_no' => 'MPV-23-HEA-P0002132',
                'client_name' => 'Jean Grey',
                'insurer' => 'SACOS',
                'policy_class' => 'Motor',
                'policy_plan' => 'Comprehensive',
                'sum_insured' => 390000.00,
                'start_date' => Carbon::create(2023, 10, 16),
                'end_date' => Carbon::create(2024, 10, 15),
                'insured' => 'S44444',
                'policy_status' => 'DFR',
                'date_registered' => Carbon::create(2024, 10, 16),
                'policy_id' => 'PL111',
                'insured_item' => 'Suzuki Fronx',
                'renewable' => 'Yes',
                'biz_type' => 'Direct',
                'term' => 1,
                'term_unit' => 'Year',
                'base_premium' => 9875.77,
                'premium' => 11455.89,
                'frequency' => 'Annually',
                'pay_plan' => 'Full',
                'agency' => null,
                'agent' => null,
                'notes' => 'New vehicle policy'
            ],
            [
                'policy_no' => 'FSP-21-P00012999',
                'client_name' => 'Barbara Walton',
                'insurer' => 'SACOS',
                'policy_class' => 'General',
                'policy_plan' => 'Householder\'s',
                'sum_insured' => null,
                'start_date' => Carbon::create(2020, 4, 18),
                'end_date' => Carbon::create(2025, 4, 17),
                'insured' => null,
                'policy_status' => 'In Force',
                'date_registered' => Carbon::create(2020, 4, 18),
                'policy_id' => 'PL110',
                'insured_item' => 'Residence at Anse Royal',
                'renewable' => 'Yes',
                'biz_type' => 'Direct',
                'term' => 1,
                'term_unit' => 'Year',
                'base_premium' => 7650.00,
                'premium' => 35467.00,
                'frequency' => 'Annually',
                'pay_plan' => 'Full',
                'agency' => null,
                'agent' => null,
                'notes' => 'Home insurance policy'
            ],
            [
                'policy_no' => 'PL-22-ALP-000033',
                'client_name' => 'Cornerstone (Pty) Ltd',
                'insurer' => 'Alliance',
                'policy_class' => 'General',
                'policy_plan' => 'Public Liability',
                'sum_insured' => null,
                'start_date' => Carbon::create(2022, 11, 30),
                'end_date' => Carbon::create(2023, 11, 29),
                'insured' => null,
                'policy_status' => 'DFR',
                'date_registered' => Carbon::create(2022, 11, 30),
                'policy_id' => 'PL109',
                'insured_item' => null,
                'renewable' => 'Yes',
                'biz_type' => 'Direct',
                'term' => 1,
                'term_unit' => 'Year',
                'base_premium' => 5000.00,
                'premium' => 5800.00,
                'frequency' => 'Annually',
                'pay_plan' => 'Full',
                'agency' => null,
                'agent' => null,
                'notes' => 'Business liability insurance'
            ],
            [
                'policy_no' => 'HS1-23-P00023132',
                'client_name' => 'Cornerstone (Pty) Ltd',
                'insurer' => 'Alliance',
                'policy_class' => 'General',
                'policy_plan' => 'Employer\'s Liability',
                'sum_insured' => null,
                'start_date' => Carbon::create(2022, 11, 12),
                'end_date' => Carbon::create(2023, 11, 11),
                'insured' => null,
                'policy_status' => 'DFR',
                'date_registered' => Carbon::create(2022, 11, 12),
                'policy_id' => 'PL108',
                'insured_item' => null,
                'renewable' => 'Yes',
                'biz_type' => 'Direct',
                'term' => 1,
                'term_unit' => 'Year',
                'base_premium' => 2500.00,
                'premium' => 2900.00,
                'frequency' => 'Annually',
                'pay_plan' => 'Instalments',
                'agency' => null,
                'agent' => null,
                'notes' => 'Employee coverage'
            ],
            [
                'policy_no' => 'FSP-19-P00024',
                'client_name' => 'Anna\'s Spa',
                'insurer' => 'SACOS',
                'policy_class' => 'General',
                'policy_plan' => 'Fire & Special Perils',
                'sum_insured' => null,
                'start_date' => Carbon::create(2023, 10, 6),
                'end_date' => Carbon::create(2024, 10, 5),
                'insured' => null,
                'policy_status' => 'Expired',
                'date_registered' => Carbon::create(2022, 10, 5),
                'policy_id' => 'PL107',
                'insured_item' => 'SPA at English River',
                'renewable' => 'Yes',
                'biz_type' => 'Direct',
                'term' => 1,
                'term_unit' => 'Year',
                'base_premium' => 3750.00,
                'premium' => 4350.00,
                'frequency' => 'Annually',
                'pay_plan' => 'Full',
                'agency' => null,
                'agent' => null,
                'notes' => 'Spa business insurance'
            ],
            [
                'policy_no' => 'MVC-18-000331',
                'client_name' => 'Brian Trapper',
                'insurer' => 'Hsavy',
                'policy_class' => 'Motor',
                'policy_plan' => 'Comprehensive',
                'sum_insured' => 285000.00,
                'start_date' => Carbon::create(2022, 11, 15),
                'end_date' => Carbon::create(2023, 11, 14),
                'insured' => 'S260',
                'policy_status' => 'In Force',
                'date_registered' => Carbon::create(2022, 11, 15),
                'policy_id' => 'PL106',
                'insured_item' => 'Toyota Hyrider',
                'renewable' => 'Yes',
                'biz_type' => 'Direct',
                'term' => 1,
                'term_unit' => 'Year',
                'base_premium' => 6652.00,
                'premium' => 7716.32,
                'frequency' => 'Annually',
                'pay_plan' => 'Full',
                'agency' => null,
                'agent' => null,
                'notes' => 'SUV insurance'
            ],
            [
                'policy_no' => 'MTC-22-000012',
                'client_name' => 'Adbul Juma',
                'insurer' => 'Alliance',
                'policy_class' => 'Motor',
                'policy_plan' => 'Third Party',
                'sum_insured' => 0.00,
                'start_date' => Carbon::create(2022, 9, 11),
                'end_date' => Carbon::create(2023, 9, 10),
                'insured' => 'S32453',
                'policy_status' => 'Cancelled',
                'date_registered' => Carbon::create(2022, 9, 11),
                'policy_id' => 'PL105',
                'insured_item' => 'Hyundai Creta',
                'renewable' => 'Yes',
                'biz_type' => 'Transfer',
                'term' => 1,
                'term_unit' => 'Year',
                'base_premium' => 1500.00,
                'premium' => 1827.00,
                'frequency' => 'Annually',
                'pay_plan' => 'Full',
                'agency' => null,
                'agent' => null,
                'notes' => 'Third party only'
            ],
            [
                'policy_no' => 'MVT-21-000324',
                'client_name' => 'Beta Center',
                'insurer' => 'Hsavy',
                'policy_class' => 'General',
                'policy_plan' => 'Fire & Special Perils',
                'sum_insured' => null,
                'start_date' => Carbon::create(2022, 12, 3),
                'end_date' => Carbon::create(2023, 12, 2),
                'insured' => null,
                'policy_status' => 'In Force',
                'date_registered' => Carbon::create(2022, 12, 3),
                'policy_id' => 'PL104',
                'insured_item' => 'Shop Office Complex Providence',
                'renewable' => 'Yes',
                'biz_type' => 'Transfer',
                'term' => 1,
                'term_unit' => 'Year',
                'base_premium' => 14377.00,
                'premium' => 16677.32,
                'frequency' => 'Annually',
                'pay_plan' => 'Full',
                'agency' => null,
                'agent' => null,
                'notes' => 'Commercial property'
            ],
            [
                'policy_no' => 'MVT-21-000324',
                'client_name' => 'Steven Drax',
                'insurer' => 'Hsavy',
                'policy_class' => 'General',
                'policy_plan' => 'House Insurance',
                'sum_insured' => null,
                'start_date' => Carbon::create(2024, 1, 4),
                'end_date' => Carbon::create(2025, 1, 3),
                'insured' => null,
                'policy_status' => 'In Force',
                'date_registered' => Carbon::create(2023, 1, 4),
                'policy_id' => 'PL103',
                'insured_item' => 'Residence at Belombre',
                'renewable' => 'Yes',
                'biz_type' => 'Direct',
                'term' => 1,
                'term_unit' => 'Year',
                'base_premium' => 8765.60,
                'premium' => 10168.10,
                'frequency' => 'Annually',
                'pay_plan' => 'Full',
                'agency' => null,
                'agent' => null,
                'notes' => 'Residential property'
            ],
            [
                'policy_no' => 'HS1-070-1-P0000435',
                'client_name' => 'Cold Cuts',
                'insurer' => 'Alliance',
                'policy_class' => 'General',
                'policy_plan' => 'Fire Industrial',
                'sum_insured' => null,
                'start_date' => Carbon::create(2022, 8, 12),
                'end_date' => Carbon::create(2023, 8, 11),
                'insured' => null,
                'policy_status' => 'Expired',
                'date_registered' => Carbon::create(2022, 8, 12),
                'policy_id' => 'PL102',
                'insured_item' => null,
                'renewable' => 'Yes',
                'biz_type' => 'Transfer',
                'term' => 1,
                'term_unit' => 'Year',
                'base_premium' => 4750.00,
                'premium' => 5510.00,
                'frequency' => 'Annually',
                'pay_plan' => 'Full',
                'agency' => null,
                'agent' => null,
                'notes' => 'Industrial fire coverage'
            ],
            [
                'policy_no' => 'MVC-22-000023',
                'client_name' => 'Atlas Cars',
                'insurer' => 'Alliance',
                'policy_class' => 'Motor',
                'policy_plan' => 'Comprehensive',
                'sum_insured' => 386000.00,
                'start_date' => Carbon::create(2022, 8, 16),
                'end_date' => Carbon::create(2023, 8, 15),
                'insured' => null,
                'policy_status' => 'In Force',
                'date_registered' => Carbon::create(2022, 8, 16),
                'policy_id' => 'PL101',
                'insured_item' => null,
                'renewable' => 'Yes',
                'biz_type' => 'Direct',
                'term' => 1,
                'term_unit' => 'Year',
                'base_premium' => 14325.55,
                'premium' => 16617.64,
                'frequency' => 'Annually',
                'pay_plan' => 'Full',
                'agency' => null,
                'agent' => null,
                'notes' => 'Fleet insurance'
            ],
            [
                'policy_no' => 'TIP-23-P67367283',
                'client_name' => 'Trevor Thomas',
                'insurer' => 'SACOS',
                'policy_class' => 'Travel',
                'policy_plan' => 'World Wide Basic',
                'sum_insured' => null,
                'start_date' => Carbon::create(2022, 8, 6),
                'end_date' => Carbon::create(2022, 8, 20),
                'insured' => null,
                'policy_status' => 'In Force',
                'date_registered' => Carbon::create(2022, 8, 6),
                'policy_id' => 'PL100',
                'insured_item' => null,
                'renewable' => 'No',
                'biz_type' => 'Direct',
                'term' => 14,
                'term_unit' => 'Days',
                'base_premium' => 1657.44,
                'premium' => 1657.44,
                'frequency' => 'One Off',
                'pay_plan' => 'Full',
                'agency' => null,
                'agent' => null,
                'notes' => 'Travel insurance'
            ],
            [
                'policy_no' => 'PHV-22-P0000233',
                'client_name' => 'Walter Cox',
                'insurer' => 'SACOS',
                'policy_class' => 'Marine',
                'policy_plan' => 'Marine Hull',
                'sum_insured' => 400000.00,
                'start_date' => Carbon::create(2022, 6, 3),
                'end_date' => Carbon::create(2023, 6, 2),
                'insured' => null,
                'policy_status' => 'In Force',
                'date_registered' => Carbon::create(2021, 6, 3),
                'policy_id' => 'PL99',
                'insured_item' => null,
                'renewable' => 'No',
                'biz_type' => 'Direct',
                'term' => 20,
                'term_unit' => 'Year',
                'base_premium' => 13456.00,
                'premium' => 13436.00,
                'frequency' => 'Monthly',
                'pay_plan' => 'Regular',
                'agency' => null,
                'agent' => null,
                'notes' => 'Boat insurance'
            ],
            [
                'policy_no' => 'HS1-22-HIN-000321',
                'client_name' => 'Tom Blakley',
                'insurer' => 'Hsavy',
                'policy_class' => 'General',
                'policy_plan' => 'House Insurance',
                'sum_insured' => 2500000.00,
                'start_date' => Carbon::create(2023, 11, 4),
                'end_date' => Carbon::create(2024, 11, 19),
                'insured' => null,
                'policy_status' => 'DFR',
                'date_registered' => Carbon::create(2022, 11, 3),
                'policy_id' => 'PL98',
                'insured_item' => null,
                'renewable' => 'No',
                'biz_type' => 'Direct',
                'term' => 12,
                'term_unit' => 'Year',
                'base_premium' => 35467.00,
                'premium' => 8874.00,
                'frequency' => 'Single',
                'pay_plan' => 'Full',
                'agency' => null,
                'agent' => null,
                'notes' => 'Long term house insurance'
            ]
        ];

        foreach ($policies as $policyData) {
            // Extract client_name and find or create client
            $clientName = $policyData['client_name'] ?? null;
            
            // Remove old string fields that no longer exist
            $fieldsToRemove = [
                'client_name', 'insurer', 'policy_class', 'policy_plan', 
                'policy_status', 'biz_type', 'frequency', 'pay_plan', 
                'agency', 'policy_id'
            ];
            foreach ($fieldsToRemove as $field) {
                unset($policyData[$field]);
            }
            
            $clientId = null;
            if ($clientName) {
                // Try to find existing client by name
                $client = Client::where('client_name', $clientName)->first();
                
                if (!$client) {
                    // Create a new client if not found
                    $nameParts = explode(' ', $clientName, 2);
                    $firstName = $nameParts[0] ?? $clientName;
                    $surname = $nameParts[1] ?? $clientName;
                    
                    $client = Client::create([
                        'client_name' => $clientName,
                        'first_name' => $firstName,
                        'surname' => $surname,
                        'client_type' => 'Individual',
                        'clid' => 'CLI' . str_pad(Client::count() + 1, 6, '0', STR_PAD_LEFT),
                        'email_address' => strtolower(str_replace([' ', "'"], ['.', ''], $clientName)) . '@example.com',
                        'mobile_no' => '00000000',
                        'source' => 'Direct',
                        'status' => 'Active',
                        'signed_up' => now(),
                    ]);
                }
                
                $clientId = $client->id;
            }
            
            // Add client_id to policy data
            $policyData['client_id'] = $clientId;
            
            // Set lookup IDs to null (they would need to be mapped from lookup values)
            $policyData['insurer_id'] = null;
            $policyData['policy_class_id'] = null;
            $policyData['policy_plan_id'] = null;
            $policyData['policy_status_id'] = null;
            $policyData['business_type_id'] = null;
            $policyData['frequency_id'] = null;
            $policyData['pay_plan_lookup_id'] = null;
            $policyData['agency_id'] = null;
            
            // Convert renewable from string to boolean
            if (isset($policyData['renewable'])) {
                $policyData['renewable'] = strtolower($policyData['renewable']) === 'yes';
            }
            
            // Generate policy_code if not set (use policy_no as fallback)
            // Make it unique by appending a counter if needed
            if (!isset($policyData['policy_code'])) {
                $baseCode = $policyData['policy_no'] ?? 'POL-' . uniqid();
                $policyCode = $baseCode;
                $counter = 1;
                
                // Check if policy_code already exists and make it unique
                while (Policy::where('policy_code', $policyCode)->exists()) {
                    $policyCode = $baseCode . '-' . $counter;
                    $counter++;
                }
                
                $policyData['policy_code'] = $policyCode;
            }
            
            Policy::create($policyData);
        }

        $this->command->info('Successfully seeded ' . count($policies) . ' policies.');
    }
}