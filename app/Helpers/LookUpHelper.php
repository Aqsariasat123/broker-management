<?php

namespace App\Helpers;
use App\Models\LookupCategory;

class LookUpHelper
{
    public static function getLookupData()
    {
        // Helper to get values from category
        $getValues = function($categoryName, $default = []) {
            $category = LookupCategory::where('name', $categoryName)->first();
            if (!$category) return $default;

            return $category->values()
                ->where('active', true)
                ->orderBy('seq')
                ->get(['id', 'name'])
                ->toArray();
        };

        return [
            'contact_types'   => $getValues('Contact Type'),
            'sources'         => $getValues('Source'),
            'agents'          => $getValues('Agent'),
            'agencies'        => $getValues('APL Agency'),
            'salutations'     => $getValues('Salutation'),
            'contact_statuses'=> $getValues('Contact Status', [
                ['id' => 1, 'name' => 'Not Contacted'],
                ['id' => 2, 'name' => 'In Discussion'],
                ['id' => 3, 'name' => 'Proposal Made'],
                ['id' => 4, 'name' => 'Keep In View'],
                ['id' => 5, 'name' => 'Archived'],
                ['id' => 6, 'name' => 'RNR'],
                ['id' => 7, 'name' => 'Differed'],
            ]),
            'ranks'           => $getValues('Rank', [
                ['id' => 1, 'name' => 'VIP'],
                ['id' => 2, 'name' => 'High'],
                ['id' => 3, 'name' => 'Medium'],
                ['id' => 4, 'name' => 'Low'],
                ['id' => 5, 'name' => 'Warm'],
            ]),
            'districts'       => $getValues('District'),
            'occupations'     => $getValues('Occupation'),
            'islands'         => $getValues('Island'),
            'countries'       => $getValues('Issuing Country'),
            'income_sources'  => $getValues('Income Source'),
        ];
    }
}
