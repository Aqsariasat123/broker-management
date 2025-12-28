<?php

namespace App\Helpers;
use App\Models\LookupCategory;
use App\Models\Client;
class LookUpHelper
{
   public static function getLookupData()
    {
       
        $getValues = function (
            string $categoryName,
            array $default = [],
            bool $useSeq = true
        ) {
            $category = LookupCategory::where('name', $categoryName)->first();

            if (!$category) {
                return $default;
            }

            $query = $category->values()
                ->where('active', true);

            if ($useSeq) {
                $query->orderBy('seq');
            }

            return $query->get(['id', 'name'])->toArray();
        };

        return [

            /* ================= CONTACT LOOKUPS ================= */

            'contact_types' => $getValues('Contact Type'),

            'sources' => $getValues('Source'),

            'agents' => $getValues('Agent'),

            'agencies' => $getValues('APL Agency'),

            'salutations' => $getValues('Salutation'),

            'contact_statuses' => $getValues('Contact Status', [
                ['id' => 1, 'name' => 'Not Contacted'],
                ['id' => 2, 'name' => 'In Discussion'],
                ['id' => 3, 'name' => 'Proposal Made'],
                ['id' => 4, 'name' => 'Keep In View'],
                ['id' => 5, 'name' => 'Archived'],
                ['id' => 6, 'name' => 'RNR'],
                ['id' => 7, 'name' => 'Differed'],
            ]),

            'ranks' => $getValues('Rank', [
                ['id' => 1, 'name' => 'VIP'],
                ['id' => 2, 'name' => 'High'],
                ['id' => 3, 'name' => 'Medium'],
                ['id' => 4, 'name' => 'Low'],
                ['id' => 5, 'name' => 'Warm'],
            ]),

            'districts' => $getValues('District'),

            'occupations' => $getValues('Occupation'),

            'islands' => $getValues('Island'),

            'countries' => $getValues('Issuing Country'),

            'income_sources' => $getValues('Income Source'),


            /* ================= POLICY LOOKUPS ================= */

            'clients' => Client::orderBy('client_name')
                ->get(['id', 'client_name', 'clid'])
                ->toArray(),

            'insurers' => $getValues('Insurers', [], false),

            'policy_classes' => $getValues('Class', [], false),

            'policy_plans' => $getValues('Policy Plans', [], false),

            'policy_statuses' => $getValues('Policy Status', [
                ['id' => null, 'name' => 'In Force'],
                ['id' => null, 'name' => 'DFR'],
                ['id' => null, 'name' => 'Expired'],
                ['id' => null, 'name' => 'Cancelled'],
            ], false),

            'business_types' => $getValues('Business Type', [
                ['id' => null, 'name' => 'Direct'],
                ['id' => null, 'name' => 'Transfer'],
            ], false),

            'term_units' => $getValues('Term Units', [
                ['id' => null, 'name' => 'Year'],
                ['id' => null, 'name' => 'Month'],
                ['id' => null, 'name' => 'Days'],
            ], false),

            'frequencies' => $getValues('Frequency', [
                ['id' => null, 'name' => 'Annually'],
                ['id' => null, 'name' => 'Monthly'],
                ['id' => null, 'name' => 'Quarterly'],
                ['id' => null, 'name' => 'One Off'],
                ['id' => null, 'name' => 'Single'],
            ], false),

            'pay_plans' => $getValues('Payment Plan', [
                ['id' => null, 'name' => 'Full'],
                ['id' => null, 'name' => 'Instalments'],
                ['id' => null, 'name' => 'Regular'],
            ], false),

            'document_types' => $getValues('Document Type', [
                ['id' => null, 'name' => 'Policy Document'],
                ['id' => null, 'name' => 'Certificate'],
                ['id' => null, 'name' => 'Claim Document'],
                ['id' => null, 'name' => 'Other Document'],
            ], false),

            'channels' => $getValues('Channel', [], false),
        ];
    }

}
