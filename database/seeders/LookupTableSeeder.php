<?php
// database/seeders/LookupTableSeeder.php

namespace Database\Seeders;

use App\Models\LookupCategory;
use App\Models\LookupValue;
use Illuminate\Database\Seeder;

class LookupTableSeeder extends Seeder
{
    public function run()
    {
        $lookupData = [
            'Contact Type' => [
                ['seq' => 1, 'name' => 'Lead', 'active' => true],
                ['seq' => 2, 'name' => 'Prospect', 'active' => true],
                ['seq' => 3, 'name' => 'Contact', 'active' => true],
                ['seq' => 4, 'name' => 'SO Bank Officer', 'active' => true],
                ['seq' => 5, 'name' => 'Payroll Officer', 'active' => true],
            ],
            'Claim Stage' => [
                ['seq' => 1, 'name' => 'Awaiting Documents', 'active' => true],
                ['seq' => 2, 'name' => 'Awaiting QS Report', 'active' => true],
            ],
            'Vehicle Make' => [
                ['seq' => 1, 'name' => 'Hyundai', 'active' => true],
                ['seq' => 2, 'name' => 'Kia', 'active' => true],
                ['seq' => 3, 'name' => 'Suzuki', 'active' => true],
                ['seq' => 4, 'name' => 'Toyota', 'active' => true],
                ['seq' => 5, 'name' => 'Ford', 'active' => true],
                ['seq' => 6, 'name' => 'MG', 'active' => true],
                ['seq' => 7, 'name' => 'Nissan', 'active' => true],
                ['seq' => 8, 'name' => 'Mazda', 'active' => true],
                ['seq' => 9, 'name' => 'BMW', 'active' => true],
                ['seq' => 10, 'name' => 'Mercedes', 'active' => true],
                ['seq' => 11, 'name' => 'Lexus', 'active' => true],
                ['seq' => 12, 'name' => 'Haval', 'active' => true],
                ['seq' => 13, 'name' => 'Honda', 'active' => true],
                ['seq' => 14, 'name' => 'Tata', 'active' => true],
                ['seq' => 15, 'name' => 'Isuzu', 'active' => true],
            ],
            'Client Type' => [
                ['seq' => 1, 'name' => 'Individual', 'active' => true],
                ['seq' => 2, 'name' => 'Business', 'active' => true],
                ['seq' => 3, 'name' => 'Company', 'active' => true],
                ['seq' => 4, 'name' => 'Organization', 'active' => true],
            ],
            'Insurer' => [
                ['seq' => 1, 'name' => 'SACOS', 'active' => true],
                ['seq' => 2, 'name' => 'HSavy', 'active' => true],
                ['seq' => 3, 'name' => 'Alliance', 'active' => true],
                ['seq' => 4, 'name' => 'MUA', 'active' => false],
            ],
            'Frequency' => [
                ['seq' => 1, 'name' => 'Year', 'active' => true],
                ['seq' => 2, 'name' => 'Days', 'active' => true],
                ['seq' => 3, 'name' => 'Weeks', 'active' => true],
            ],
            'Payment Plan' => [
                ['seq' => 1, 'name' => 'Single', 'active' => true],
                ['seq' => 2, 'name' => 'Instalments', 'active' => true],
                ['seq' => 3, 'name' => 'Regular (Life)', 'active' => true],
            ],
            'Contact Stage' => [
                ['seq' => 1, 'name' => 'Open', 'active' => true],
                ['seq' => 2, 'name' => 'Qualified', 'active' => true],
                ['seq' => 3, 'name' => 'KIV', 'active' => true],
                ['seq' => 4, 'name' => 'Closed', 'active' => true],
            ],
            'Source' => [
                ['seq' => 1, 'name' => 'Direct', 'active' => true],
                ['seq' => 2, 'name' => 'Online', 'active' => true],
                ['seq' => 3, 'name' => 'Bank ABSA', 'active' => true],
                ['seq' => 4, 'name' => 'MCB', 'active' => true],
                ['seq' => 5, 'name' => 'NOU', 'active' => true],
                ['seq' => 6, 'name' => 'BAR', 'active' => true],
                ['seq' => 7, 'name' => 'BOC', 'active' => true],
                ['seq' => 8, 'name' => 'SCB', 'active' => true],
                ['seq' => 9, 'name' => 'SCU', 'active' => true],
                ['seq' => 10, 'name' => 'AIRTEL', 'active' => true],
                ['seq' => 11, 'name' => 'Cable & Wireless', 'active' => true],
                ['seq' => 12, 'name' => 'Intelvision', 'active' => true],
                ['seq' => 13, 'name' => 'PUC', 'active' => true],
                ['seq' => 14, 'name' => 'SFA', 'active' => true],
                ['seq' => 15, 'name' => 'STC', 'active' => true],
                ['seq' => 16, 'name' => 'FSA', 'active' => true],
                ['seq' => 17, 'name' => 'Mins Of Education', 'active' => true],
                ['seq' => 18, 'name' => 'Mins Of Health', 'active' => true],
                ['seq' => 19, 'name' => 'SFRSA', 'active' => true],
                ['seq' => 20, 'name' => 'Seychelles Police', 'active' => true],
                ['seq' => 21, 'name' => 'Treasury', 'active' => true],
                ['seq' => 22, 'name' => 'Judiciary', 'active' => true],
                ['seq' => 23, 'name' => 'Pilgrims', 'active' => true],
                ['seq' => 24, 'name' => 'SPTC', 'active' => true],
            ],
            'Contact Status' => [
                ['seq' => 1, 'name' => 'Not Contacted', 'active' => true],
                ['seq' => 2, 'name' => 'Qualified', 'active' => true],
                ['seq' => 3, 'name' => 'Converted to Client', 'active' => true],
                ['seq' => 4, 'name' => 'Keep In View', 'active' => true],
                ['seq' => 5, 'name' => 'Archived', 'active' => true],
            ],
            'Policy Status' => [
                ['seq' => 1, 'name' => 'In Force', 'active' => true],
                ['seq' => 2, 'name' => 'Expired', 'active' => true],
                ['seq' => 3, 'name' => 'Cancelled', 'active' => true],
                ['seq' => 4, 'name' => 'Lapsed', 'active' => true],
                ['seq' => 5, 'name' => 'Matured', 'active' => true],
                ['seq' => 6, 'name' => 'Surrenders', 'active' => true],
                ['seq' => 7, 'name' => 'Payout D', 'active' => true],
                ['seq' => 8, 'name' => 'Payout TPD', 'active' => true],
                ['seq' => 9, 'name' => 'Null & Void', 'active' => true],
            ],
            'APL Agency' => [
                ['seq' => 1, 'name' => 'Keystone', 'active' => true],
                ['seq' => 2, 'name' => 'LIS', 'active' => true],
            ],
            'Channel' => [
                ['seq' => 1, 'name' => 'Direct', 'active' => true],
                ['seq' => 2, 'name' => 'Online', 'active' => true],
                ['seq' => 3, 'name' => 'Agent', 'active' => true],
                ['seq' => 4, 'name' => 'Broker', 'active' => true],
                ['seq' => 5, 'name' => 'Referral', 'active' => true],
            ],
            'Payment Status' => [
                ['seq' => 1, 'name' => 'Paid', 'active' => true],
                ['seq' => 2, 'name' => 'Partly Paid', 'active' => true],
                ['seq' => 3, 'name' => 'Unpaid', 'active' => true],
            ],
            'Agent' => [
                ['seq' => 1, 'name' => 'Mandy', 'active' => true],
                ['seq' => 2, 'name' => 'Simon', 'active' => true],
            ],
            'Ranking' => [
                ['seq' => 1, 'name' => 'VIP', 'active' => true],
                ['seq' => 2, 'name' => 'High', 'active' => true],
                ['seq' => 3, 'name' => 'Medium', 'active' => true],
                ['seq' => 4, 'name' => 'Low', 'active' => true],
            ],
            'Rank' => [
                ['seq' => 1, 'name' => 'VIP', 'active' => true],
                ['seq' => 2, 'name' => 'High', 'active' => true],
                ['seq' => 3, 'name' => 'Medium', 'active' => true],
                ['seq' => 4, 'name' => 'Low', 'active' => true],
                ['seq' => 5, 'name' => 'Warm', 'active' => true],
            ],
            'Client Status' => [
                ['seq' => 1, 'name' => 'Active', 'active' => true],
                ['seq' => 2, 'name' => 'Inactive', 'active' => true],
                ['seq' => 3, 'name' => 'Suspended', 'active' => true],
                ['seq' => 4, 'name' => 'Pending', 'active' => true],
                ['seq' => 5, 'name' => 'Dormant', 'active' => true],
            ],
            'Issuing Country' => [
                ['seq' => 1, 'name' => 'Seychelles', 'code' => 'SEY', 'active' => true],
                ['seq' => 2, 'name' => 'Great Britain', 'code' => 'GBR', 'active' => true],
                ['seq' => 3, 'name' => 'Botswana', 'code' => 'BOT', 'active' => true],
                ['seq' => 4, 'name' => 'Sri Lanka', 'code' => 'SRI', 'active' => true],
                ['seq' => 5, 'name' => 'India', 'code' => 'IND', 'active' => true],
                ['seq' => 6, 'name' => 'Nepal', 'code' => 'NEP', 'active' => true],
                ['seq' => 7, 'name' => 'Bangladesh', 'code' => 'BAN', 'active' => true],
                ['seq' => 8, 'name' => 'Russia', 'code' => 'RUS', 'active' => true],
                ['seq' => 9, 'name' => 'Ukraine', 'code' => 'UKR', 'active' => true],
                ['seq' => 10, 'name' => 'Kenya', 'code' => 'KEN', 'active' => true],
            ],
            'Source Of Payment' => [
                ['seq' => 1, 'name' => 'Commission', 'active' => true],
                ['seq' => 2, 'name' => 'Bonus', 'active' => true],
                ['seq' => 3, 'name' => 'Prize', 'active' => true],
                ['seq' => 4, 'name' => 'Other', 'active' => true],
            ],
            'ID Type' => [
                ['seq' => 1, 'name' => 'ID Card', 'active' => true],
                ['seq' => 2, 'name' => 'Driving License', 'active' => true],
                ['seq' => 3, 'name' => 'Passport', 'active' => true],
            ],
            'Class' => [
                ['seq' => 1, 'name' => 'Motor', 'active' => true],
                ['seq' => 2, 'name' => 'General', 'active' => true],
                ['seq' => 3, 'name' => 'Life', 'active' => true],
                ['seq' => 4, 'name' => 'Bonds', 'active' => true],
                ['seq' => 5, 'name' => 'Travel', 'active' => true],
                ['seq' => 6, 'name' => 'Marine', 'active' => true],
                ['seq' => 7, 'name' => 'Health', 'active' => false],
            ],
            'Island' => [
                ['seq' => 1, 'name' => 'Mahe', 'active' => true],
                ['seq' => 2, 'name' => 'Praslin', 'active' => true],
                ['seq' => 3, 'name' => 'La Digue', 'active' => true],
                ['seq' => 4, 'name' => 'Perseverance', 'active' => true],
                ['seq' => 5, 'name' => 'Cerf', 'active' => true],
                ['seq' => 6, 'name' => 'Eden', 'active' => true],
                ['seq' => 7, 'name' => 'Silhouette', 'active' => true],
            ],
            'Mode Of Payment (Life)' => [
                ['seq' => 1, 'name' => 'Transfer', 'active' => true],
                ['seq' => 2, 'name' => 'Cheque', 'active' => true],
                ['seq' => 3, 'name' => 'Cash', 'active' => true],
                ['seq' => 4, 'name' => 'Online', 'active' => true],
                ['seq' => 5, 'name' => 'Standing Order', 'active' => true],
                ['seq' => 6, 'name' => 'Salary Deduction', 'active' => true],
                ['seq' => 7, 'name' => 'Direect', 'active' => true],
            ],
            'Claim Status' => [
                ['seq' => 1, 'name' => 'Processing', 'active' => true],
                ['seq' => 2, 'name' => 'Settled', 'active' => true],
                ['seq' => 3, 'name' => 'Declined', 'active' => true],
            ],
            'Salutation' => [
                ['seq' => 1, 'name' => 'Mr', 'active' => true],
                ['seq' => 2, 'name' => 'Ms', 'active' => true],
                ['seq' => 3, 'name' => 'Mrs', 'active' => true],
                ['seq' => 4, 'name' => 'Miss', 'active' => true],
                ['seq' => 5, 'name' => 'Dr', 'active' => true],
                ['seq' => 6, 'name' => 'Mr & Mrs', 'active' => true],
            ],
            'Mode Of Payment (General)' => [
                ['seq' => 1, 'name' => 'Cash', 'active' => true],
                ['seq' => 2, 'name' => 'Card', 'active' => true],
                ['seq' => 3, 'name' => 'Transfer', 'active' => true],
                ['seq' => 4, 'name' => 'Cheque', 'active' => true],
            ],
            'Useage' => [
                ['seq' => 1, 'name' => 'Private', 'active' => true],
                ['seq' => 2, 'name' => 'Commercial', 'active' => true],
                ['seq' => 3, 'name' => 'For Hire', 'active' => true],
                ['seq' => 4, 'name' => 'Carriage Of Goods', 'active' => true],
                ['seq' => 5, 'name' => 'Commuter', 'active' => true],
            ],
            'Expense Category' => [
                ['seq' => 1, 'name' => 'License', 'active' => true],
                ['seq' => 2, 'name' => 'Insurance', 'active' => true],
                ['seq' => 3, 'name' => 'Office supplies', 'active' => true],
                ['seq' => 4, 'name' => 'Telephone & Internet', 'active' => true],
                ['seq' => 5, 'name' => 'Marketting', 'active' => true],
                ['seq' => 6, 'name' => 'Travel', 'active' => true],
                ['seq' => 7, 'name' => 'Referals', 'active' => true],
                ['seq' => 8, 'name' => 'Rentals', 'active' => true],
                ['seq' => 9, 'name' => 'Vehicle', 'active' => true],
                ['seq' => 10, 'name' => 'Fuel', 'active' => true],
                ['seq' => 11, 'name' => 'Bank Fees', 'active' => true],
                ['seq' => 12, 'name' => 'Charges', 'active' => true],
                ['seq' => 13, 'name' => 'Misc', 'active' => true],
                ['seq' => 14, 'name' => 'Asset Purchase', 'active' => true],
            ],
            'Vehicle Type' => [
                ['seq' => 1, 'name' => 'SUV', 'active' => true],
                ['seq' => 2, 'name' => 'Hatchback', 'active' => true],
                ['seq' => 3, 'name' => 'Sedan', 'active' => true],
                ['seq' => 4, 'name' => 'Twin Cab', 'active' => true],
                ['seq' => 5, 'name' => 'Pick Up', 'active' => true],
                ['seq' => 6, 'name' => 'Scooter', 'active' => true],
                ['seq' => 7, 'name' => 'Motor Cycle', 'active' => true],
                ['seq' => 8, 'name' => 'Taxi', 'active' => true],
                ['seq' => 9, 'name' => 'Van', 'active' => true],
            ],
            'Income Category' => [
                ['seq' => 1, 'name' => 'General', 'active' => true],
                ['seq' => 2, 'name' => 'Commission', 'active' => true],
                ['seq' => 3, 'name' => 'Bonus', 'active' => true],
                ['seq' => 4, 'name' => 'Salary', 'active' => true],
                ['seq' => 5, 'name' => 'Investment', 'active' => true],
                ['seq' => 6, 'name' => 'Rentals', 'active' => true],
                ['seq' => 7, 'name' => 'Other', 'active' => true],
            ],
            'Business Type' => [
                ['seq' => 1, 'name' => 'Direct', 'active' => true],
                ['seq' => 2, 'name' => 'Transfer', 'active' => true],
                ['seq' => 3, 'name' => 'Renewal', 'active' => true],
            ],
            'Income Source' => [
                ['seq' => 1, 'name' => 'Employment', 'active' => true],
                ['seq' => 2, 'name' => 'Self Employed', 'active' => true],
                ['seq' => 3, 'name' => 'Business', 'active' => true],
                ['seq' => 4, 'name' => 'Investment', 'active' => true],
                ['seq' => 5, 'name' => 'Rentals', 'active' => true],
                ['seq' => 6, 'name' => 'Retirement', 'active' => true],
                ['seq' => 7, 'name' => 'Allowance', 'active' => true],
                ['seq' => 8, 'name' => 'Other', 'active' => true],
            ],
            'Proposal Stage' => [
                ['seq' => 1, 'name' => 'Not Contacted', 'active' => true],
                ['seq' => 2, 'name' => 'RNR', 'active' => true],
                ['seq' => 3, 'name' => 'In Discussion', 'active' => true],
                ['seq' => 4, 'name' => 'Offer Made', 'active' => true],
                ['seq' => 5, 'name' => 'Proposal Filled', 'active' => true],
            ],
            'Proposal Status' => [
                ['seq' => 1, 'name' => 'Awaiting Medical', 'active' => true],
                ['seq' => 2, 'name' => 'Awaiting Policy', 'active' => true],
                ['seq' => 3, 'name' => 'Approved', 'active' => true],
                ['seq' => 4, 'name' => 'Declined', 'active' => true],
                ['seq' => 5, 'name' => 'Withdrawn', 'active' => true],
            ],
            'PaymentType' => [
                ['seq' => 1, 'name' => 'Full', 'active' => true],
                ['seq' => 2, 'name' => 'Instalment', 'active' => true],
                ['seq' => 3, 'name' => 'Adjustment', 'active' => true],
            ],
            'Term' => [
                ['seq' => 1, 'name' => 'Annual', 'active' => true],
                ['seq' => 2, 'name' => 'Single', 'active' => true],
                ['seq' => 3, 'name' => 'Monthly', 'active' => true],
                ['seq' => 4, 'name' => 'Quarterly', 'active' => true],
                ['seq' => 5, 'name' => 'Bi-Annual', 'active' => true],
            ],
            'Engine Type' => [
                ['seq' => 1, 'name' => 'Hybrid', 'active' => true],
                ['seq' => 2, 'name' => 'Petrol', 'active' => true],
                ['seq' => 3, 'name' => 'Diesel', 'active' => true],
                ['seq' => 4, 'name' => 'Electric', 'active' => true],
            ],
            'ENDORSEMENT' => [
                ['seq' => 1, 'name' => 'Renewal', 'description' => 'Policy Renewed', 'active' => true],
                ['seq' => 2, 'name' => 'Cancelation', 'description' => 'Policy Cancelled', 'active' => true],
                ['seq' => 3, 'name' => 'Amendment', 'description' => 'Sum Insured Reduced', 'active' => true],
                ['seq' => 4, 'name' => 'Amendment', 'description' => 'Sum Insured Increased', 'active' => true],
                ['seq' => 5, 'name' => 'Amendment', 'description' => 'Plan Cover Changed', 'active' => true],
                ['seq' => 6, 'name' => 'Amendment', 'description' => 'Beneficary change', 'active' => true],
                ['seq' => 7, 'name' => 'Amendment', 'description' => 'Pay Plan Changed', 'active' => true],
                ['seq' => 8, 'name' => 'Amendment', 'description' => 'Vehicle changed', 'active' => true],
            ],
            'District' => [
                ['seq' => 1, 'name' => 'Victoria', 'active' => true],
                ['seq' => 2, 'name' => 'Beau Vallon', 'active' => true],
                ['seq' => 3, 'name' => 'Mont Fleuri', 'active' => true],
                ['seq' => 4, 'name' => 'Cascade', 'active' => true],
                ['seq' => 5, 'name' => 'Providence', 'active' => true],
                ['seq' => 6, 'name' => 'Grand Anse', 'active' => true],
                ['seq' => 7, 'name' => 'Anse Aux Pins', 'active' => true],
            ],
            'Occupation' => [
                ['seq' => 1, 'name' => 'Accountant', 'active' => true],
                ['seq' => 2, 'name' => 'Driver', 'active' => true],
                ['seq' => 3, 'name' => 'Customer Service Officer', 'active' => true],
                ['seq' => 4, 'name' => 'Real Estate Agent', 'active' => true],
                ['seq' => 5, 'name' => 'Rock Breaker', 'active' => true],
                ['seq' => 6, 'name' => 'Payroll Officer', 'active' => true],
                ['seq' => 7, 'name' => 'Boat Charter', 'active' => true],
                ['seq' => 8, 'name' => 'Contractor', 'active' => true],
                ['seq' => 9, 'name' => 'Technician', 'active' => true],
                ['seq' => 10, 'name' => 'Paymaster', 'active' => true],
                ['seq' => 11, 'name' => 'Human Resources Manager', 'active' => true],
            ],
            'Term Units' => [
                ['seq' => 1, 'name' => 'Year', 'active' => true],
                ['seq' => 2, 'name' => 'Month', 'active' => true],
                ['seq' => 3, 'name' => 'Days', 'active' => true],
            ],
            'Document Type' => [
                ['seq' => 1, 'name' => 'Policy Document', 'active' => true],
                ['seq' => 2, 'name' => 'Certificate', 'active' => true],
                ['seq' => 3, 'name' => 'Claim Document', 'active' => true],
                ['seq' => 4, 'name' => 'Other Document', 'active' => true],
            ],

            'Task Category' => [
                ['seq' => 1, 'name' => 'Payment', 'active' => true],
                ['seq' => 2, 'name' => 'Report', 'active' => true],
                ['seq' => 3, 'name' => 'Follow-up', 'active' => true],
                ['seq' => 4, 'name' => 'Meeting', 'active' => true],
                ['seq' => 5, 'name' => 'Call', 'active' => true],
            ],
        ];

        foreach ($lookupData as $categoryName => $values) {
            $category = LookupCategory::firstOrCreate(
                ['name' => $categoryName],
                ['active' => true]
            );

            foreach ($values as $value) {
                // Check if value already exists to avoid duplicates (by name and seq)
                $existingValue = $category->values()
                    ->where('name', $value['name'])
                    ->where('seq', $value['seq'])
                    ->first();
                
                if (!$existingValue) {
                    // Also check if seq already exists for this category with different name
                    $seqExists = $category->values()
                        ->where('seq', $value['seq'])
                        ->where('name', '!=', $value['name'])
                        ->exists();
                    
                    if (!$seqExists) {
                        $category->values()->create($value);
                    } else {
                        // If seq exists but name is different, create with next available seq
                        $maxSeq = $category->values()->max('seq') ?? 0;
                        $value['seq'] = $maxSeq + 1;
                        $category->values()->create($value);
                    }
                }
            }
        }
        
        $this->command->info('Lookup categories and values seeded successfully.');
    }
}