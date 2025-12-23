<?php

namespace App\Helpers;

class TableConfigHelper
{
    /**
     * Get table configuration for a module
     */
    public static function getConfig($module)
    {
        $configs = [
            'contacts' => [
                'module' => 'contacts',
                'route_prefix' => 'contacts',
                'session_key' => 'contact_columns',
                'default_columns' => [
                    'contact_name','contact_no','type','occupation','employer','acquired','source','status',
                    'rank','first_contact','next_follow_up','coid','dob','salutation','source_name','agency',
                    'agent','address','email_address','contact_id','savings_budget','married','children',
                    'children_details','vehicle','house','business','other'
                ],
                'mandatory_columns' => ['contact_name', 'type', 'source', 'status', 'salutation', 'contact_id'],
                'column_definitions' => [
                    'contact_name' => 'Contact Name',
                    'contact_no' => 'Contact No',
                    'type' => 'Type',
                    'occupation' => 'Occupation',
                    'employer' => 'Employer',
                    'acquired' => 'Acquired',
                    'source' => 'Source',
                    'status' => 'Status',
                    'rank' => 'Rank',
                    'first_contact' => '1st Contact',
                    'next_follow_up' => 'Next FU',
                    'coid' => 'COID',
                    'dob' => 'DOB',
                    'salutation' => 'Salutation',
                    'source_name' => 'Source Name',
                    'agency' => 'Agency',
                    'agent' => 'Agent',
                    'address' => 'Address',
                    'email_address' => 'Email Address',
                    'contact_id' => 'Contact ID',
                    'savings_budget' => 'Savings Budget',
                    'married' => 'Married',
                    'children' => 'Children',
                    'children_details' => 'Children Details',
                    'vehicle' => 'Vehicle',
                    'house' => 'House',
                    'business' => 'Business',
                    'other' => 'Other',
                ],
            ],
            'policies' => [
                'module' => 'policies',
                'route_prefix' => 'policies',
                'session_key' => 'policy_columns',
                'default_columns' => [
                    'policy_no','client_name','insurer','policy_class','policy_plan','sum_insured','start_date',
                    'end_date','insured','policy_status','date_registered','policy_id','insured_item','renewable',
                    'biz_type','term','term_unit','base_premium','premium','frequency','pay_plan','agency','agent','notes'
                ],
                'mandatory_columns' => ['policy_no', 'client_name', 'policy_status'],
                'column_definitions' => [
                    'policy_no' => 'Policy No',
                    'client_name' => 'Client Name',
                    'insurer' => 'Insurer',
                    'policy_class' => 'Policy Class',
                    'policy_plan' => 'Policy Plan',
                    'sum_insured' => 'Sum Insured',
                    'start_date' => 'Start Date',
                    'end_date' => 'End Date',
                    'insured' => 'Insured',
                    'policy_status' => 'Policy Status',
                    'date_registered' => 'Date Registered',
                    'policy_id' => 'Policy ID',
                    'insured_item' => 'Insured Item',
                    'renewable' => 'Renewable',
                    'biz_type' => 'Business Type',
                    'term' => 'Term',
                    'term_unit' => 'Term Unit',
                    'base_premium' => 'Base Premium',
                    'premium' => 'Premium',
                    'frequency' => 'Frequency',
                    'pay_plan' => 'Pay Plan',
                    'agency' => 'Agency',
                    'agent' => 'Agent',
                    'notes' => 'Notes',
                ],
            ],
            'vehicles' => [
                'module' => 'vehicles',
                'route_prefix' => 'vehicles',
                'session_key' => 'vehicle_columns',
                'default_columns' => [
                    'regn_no','make','model','type','useage','year','value','policy_id','engine',
                    'engine_type','cc','engine_no','chassis_no','from','to','notes','vehicle_id'
                ],
                'mandatory_columns' => ['regn_no', 'make', 'model'],
                'column_definitions' => [
                    'regn_no' => 'Registration No',
                    'make' => 'Make',
                    'model' => 'Model',
                    'type' => 'Type',
                    'useage' => 'Usage',
                    'year' => 'Year',
                    'value' => 'Value',
                    'policy_id' => 'Policy ID',
                    'engine' => 'Engine',
                    'engine_type' => 'Engine Type',
                    'cc' => 'CC',
                    'engine_no' => 'Engine No',
                    'chassis_no' => 'Chassis No',
                    'from' => 'From',
                    'to' => 'To',
                    'notes' => 'Notes',
                    'vehicle_id' => 'Vehicle ID',
                ],
            ],
            'nominees' => [
                'module' => 'nominees',
                'route_prefix' => 'nominees',
                'session_key' => 'nominee_columns',
                'default_columns' => [
                    'full_name','date_of_birth','age','nin_passport_no','relationship','share_percentage',
                    'date_added','date_removed','notes','nominee_code'
                ],
                'mandatory_columns' => ['full_name'],
                'column_definitions' => [
                    'full_name' => 'Full Name',
                    'date_of_birth' => 'DOB',
                    'age' => 'Age',
                    'nin_passport_no' => 'NIN/Passport No',
                    'relationship' => 'Relationship',
                    'share_percentage' => 'Shares',
                    'date_added' => 'Date Added',
                    'date_removed' => 'Date Removed',
                    'notes' => 'Notes',
                    'nominee_code' => 'Nominee Code',
                ],
            ],
            'documents' => [
                'module' => 'documents',
                'route_prefix' => 'documents',
                'session_key' => 'document_columns',
                'default_columns' => ['doc_id','tied_to','name','group','type','format','date_added','year','file_path','notes'],
                'mandatory_columns' => ['doc_id', 'name'],
                'column_definitions' => [
                    'doc_id' => 'DocID',
                    'tied_to' => 'Tied To',
                    'name' => 'Name',
                    'group' => 'Group',
                    'type' => 'Type',
                    'format' => 'Format',
                    'date_added' => 'Date Added',
                    'year' => 'Year',
                    'file_path' => 'File',
                    'notes' => 'Notes',
                ],
            ],
            'life-proposals' => [
                'module' => 'life-proposals',
                'route_prefix' => 'life-proposals',
                'session_key' => 'life_proposal_columns',
                'default_columns' => [
                    'proposers_name','insurer','policy_plan','sum_assured','term','add_ons','offer_date',
                    'premium','frequency','stage','date','age','status','source_of_payment','mcr','doctor',
                    'date_sent','date_completed','notes','agency','prid','class','is_submitted'
                ],
                'mandatory_columns' => ['proposers_name', 'insurer', 'policy_plan', 'status'],
                'column_definitions' => [
                    'proposers_name' => 'Proposer\'s Name',
                    'insurer' => 'Insurer',
                    'policy_plan' => 'Policy Plan',
                    'sum_assured' => 'Sum Assured',
                    'term' => 'Term',
                    'add_ons' => 'Add Ons',
                    'offer_date' => 'Offer Date',
                    'premium' => 'Premium',
                    'frequency' => 'Freq',
                    'stage' => 'Stage',
                    'date' => 'Date',
                    'age' => 'Age',
                    'status' => 'Status',
                    'source_of_payment' => 'Source Of Payment',
                    'mcr' => 'MCR',
                    'doctor' => 'Doctor',
                    'date_sent' => 'Date Sent',
                    'date_completed' => 'Date Completed',
                    'notes' => 'Notes',
                    'agency' => 'Agency',
                    'prid' => 'PRID',
                    'class' => 'Class',
                    'is_submitted' => 'Submitted',
                ],
            ],
            'tasks' => [
                'module' => 'tasks',
                'route_prefix' => 'tasks',
                'session_key' => 'task_columns',
                'default_columns' => [
                    'task_id','category','item','description','name','contact_no','due_date','due_time','due_in',
                    'date_in','assignee','task_status','date_done','repeat','frequency','rpt_date','rpt_stop_date'
                ],
                'mandatory_columns' => ['task_id', 'description', 'task_status'],
                'column_definitions' => [
                    'task_id' => 'Task ID',
                    'category' => 'Category',
                    'item' => 'Item',
                    'description' => 'Description',
                    'name' => 'Name',
                    'contact_no' => 'Contact No',
                    'due_date' => 'Due Date',
                    'due_time' => 'Time',
                    'due_in' => 'Due in',
                    'date_in' => 'Date In',
                    'assignee' => 'Assignee',
                    'task_status' => 'Task Status',
                    'date_done' => 'Date Done',
                    'repeat' => 'Repeat',
                    'frequency' => 'Frequency',
                    'rpt_date' => 'Rpt Date',
                    'rpt_stop_date' => 'Rpt Stop Date',
                ],
            ],
            'expenses' => [
                'module' => 'expenses',
                'route_prefix' => 'expenses',
                'session_key' => 'expense_columns',
                'default_columns' => [
                    'expense_id','payee','date_paid','amount_paid','description','category_id','mode_of_payment','expense_notes'
                ],
                'mandatory_columns' => ['expense_id', 'payee', 'amount_paid'],
                'column_definitions' => [
                    'expense_id' => 'Expense ID',
                    'payee' => 'Payee',
                    'date_paid' => 'Date Paid',
                    'amount_paid' => 'Amount Paid',
                    'description' => 'Description',
                    'category_id' => 'Category',
                    'mode_of_payment' => 'Mode Of Payment',
                    'expense_notes' => 'Expense Notes',
                ],
            ],
            'claims' => [
                'module' => 'claims',
                'route_prefix' => 'claims',
                'session_key' => 'claim_columns',
                'default_columns' => [
                    'claim_id','policy_no','client_name','loss_date','claim_date','claim_amount','claim_summary','status','close_date','paid_amount','settlment_notes'
                ],
                'mandatory_columns' => ['claim_id', 'policy_no', 'status'],
                'column_definitions' => [
                    'claim_id' => 'Claim ID',
                    'policy_no' => 'Policy No',
                    'client_name' => 'Client Name',
                    'loss_date' => 'Loss Date',
                    'claim_date' => 'Claim Date',
                    'claim_amount' => 'Claim Amount',
                    'claim_summary' => 'Claim Summary',
                    'status' => 'Status',
                    'close_date' => 'Close Date',
                    'paid_amount' => 'Paid Amount',
                    'settlment_notes' => 'Settlment Notes',
                ],
            ],
            'incomes' => [
                'module' => 'incomes',
                'route_prefix' => 'incomes',
                'session_key' => 'income_columns',
                'default_columns' => [
                    'income_id','income_source','date_rcvd','amount_received','description','category_id','mode_of_payment','statement_no','income_notes'
                ],
                'mandatory_columns' => ['income_id', 'income_source', 'amount_received'],
                'column_definitions' => [
                    'income_id' => 'IncomeID',
                    'income_source' => 'Income Source',
                    'date_rcvd' => 'Date Rcvd',
                    'amount_received' => 'Amount Received',
                    'description' => 'Description',
                    'category_id' => 'Category',
                    'mode_of_payment' => 'Mode Of Payment (Life)',
                    'statement_no' => 'Statement No',
                    'income_notes' => 'Income Notes',
                ],
            ],
            'endorsements' => [
                'module' => 'endorsements',
                'route_prefix' => 'endorsements',
                'session_key' => 'endorsements_columns',
                'default_columns' => [
                    'endorsement_id','income_source',
                ],
                'mandatory_columns' => ['endorsement_id', 'endorsement_no', 'policy_no','date','type','description', 'notes'],
                'column_definitions' => [
                    'endorsement_id' => 'EndorsementID',
                    'endorsement_no'=> 'Endorsement No',
                    'policy_no'=> 'Policy No',
                    'date'=> 'Date',
                    'type'=> 'Type',
                    'description'=> 'Description',
                    'notes'=> 'Notes'

                ],
            ],
            'commissions' => [
                'module' => 'commissions',
                'route_prefix' => 'commissions',
                'session_key' => 'commission_columns',
                'default_columns' => [
                    'policy_number','client_name','insurer','grouping','basic_premium','rate',
                    'amount_due','payment_status','amount_rcvd','date_rcvd','state_no',
                    'mode_of_payment','variance','reason','date_due','cnid'
                ],
                'mandatory_columns' => ['policy_number', 'client_name', 'cnid'],
                'column_definitions' => [
                    'policy_number' => 'Policy Number',
                    'client_name' => 'Client\'s Name',
                    'insurer' => 'Insurer',
                    'grouping' => 'Grouping',
                    'basic_premium' => 'Basic Premium',
                    'rate' => 'Rate',
                    'amount_due' => 'Amount Due',
                    'payment_status' => 'Payment Status',
                    'amount_rcvd' => 'Amount Rcvd',
                    'date_rcvd' => 'Date Rcvd',
                    'state_no' => 'State No',
                    'mode_of_payment' => 'Mode Of Payment (Life)',
                    'variance' => 'Variance',
                    'reason' => 'Reason',
                    'date_due' => 'Date Due',
                    'cnid' => 'CNID',
                ],
            ],
            'statements' => [
                'module' => 'statements',
                'route_prefix' => 'statements',
                'session_key' => 'statement_columns',
                'default_columns' => [
                    'statement_no','year','insurer','business_category','date_received',
                    'amount_received','mode_of_payment','remarks'
                ],
                'mandatory_columns' => ['statement_no', 'year'],
                'column_definitions' => [
                    'statement_no' => 'Statement No',
                    'year' => 'Year',
                    'insurer' => 'Insurer',
                    'business_category' => 'Business Category',
                    'date_received' => 'Date Received',
                    'amount_received' => 'Amount Received',
                    'mode_of_payment' => 'Mode Of Payment (Life)',
                    'remarks' => 'Remarks',
                ],
            ],
            'commissions' => [
                'module' => 'commissions',
                'route_prefix' => 'commissions',
                'session_key' => 'commission_columns',
                'default_columns' => [
                    'policy_number','client_name','insurer','grouping','basic_premium','rate','amount_due',
                    'payment_status','amount_rcvd','date_rcvd','state_no','mode_of_payment','variance','reason','date_due','cnid'
                ],
                'mandatory_columns' => ['policy_number', 'client_name', 'cnid'],
                'column_definitions' => [
                    'policy_number' => 'Policy Number',
                    'client_name' => "Client's Name",
                    'insurer' => 'Insurer',
                    'grouping' => 'Grouping',
                    'basic_premium' => 'Basic Premium',
                    'rate' => 'Rate',
                    'amount_due' => 'Amount Due',
                    'payment_status' => 'Payment Status',
                    'amount_rcvd' => 'Amount Rcvd',
                    'date_rcvd' => 'Date Rcvd',
                    'state_no' => 'State No',
                    'mode_of_payment' => 'Mode Of Payment (Life)',
                    'variance' => 'Variance',
                    'reason' => 'Reason',
                    'date_due' => 'Date Due',
                    'cnid' => 'CNID'
                ],
            ],
            'payment-plans' => [
                'module' => 'payment-plans',
                'route_prefix' => 'payment-plans',
                'session_key' => 'payment_plan_columns',
                'default_columns' => [
                    'installment_label','policy_no','client_name','due_date','amount','frequency','status'
                ],
                'mandatory_columns' => ['installment_label', 'due_date', 'amount', 'status'],
                'column_definitions' => [
                    'installment_label' => 'Instalment Label',
                    'policy_no' => 'Policy',
                    'client_name' => 'Client',
                    'due_date' => 'Due Date',
                    'amount' => 'Amount',
                    'frequency' => 'Frequency',
                    'status' => 'Status',
                ],
            ],
            'debit-notes' => [
                'module' => 'debit-notes',
                'route_prefix' => 'debit-notes',
                'session_key' => 'debit_note_columns',
                'default_columns' => [
                    'debit_note_no','policy_no','client_name','issued_on','amount','status'
                ],
                'mandatory_columns' => ['debit_note_no', 'issued_on', 'amount', 'status'],
                'column_definitions' => [
                    'debit_note_no' => 'Debit Note No',
                    'policy_no' => 'Policy',
                    'client_name' => 'Client',
                    'issued_on' => 'Issued On',
                    'amount' => 'Amount',
                    'status' => 'Status',
                ],
            ],
            'payments' => [
                'module' => 'payments',
                'route_prefix' => 'payments',
                'session_key' => 'payment_columns',
                'default_columns' => [
                    'payment_reference','policy_no','client_name','debit_note_no','paid_on','amount','mode_of_payment'
                ],
                'mandatory_columns' => ['payment_reference', 'paid_on', 'amount'],
                'column_definitions' => [
                    'payment_reference' => 'Payment Reference',
                    'policy_no' => 'Policy',
                    'client_name' => 'Client',
                    'debit_note_no' => 'Debit Note',
                    'paid_on' => 'Paid On',
                    'amount' => 'Amount',
                    'mode_of_payment' => 'Mode Of Payment',
                ],
            ],
            'beneficial_owners' => [
                'module' => 'beneficial_owners',
                'route_prefix' => 'beneficial-owners',
                'session_key' => 'beneficial_owner_columns',
                'default_columns' => [
                    'full_name','dob','age','nin_passport_no','country','expiry_date','status',
                    'position','shares','pep','pep_details','date_added','removed'
                ],
                'mandatory_columns' => ['full_name'],
                'column_definitions' => [
                    'full_name' => 'Full Name',
                    'dob' => 'DOB',
                    'age' => 'Age',
                    'nin_passport_no' => 'NIN/Passport No',
                    'country' => 'Country',
                    'expiry_date' => 'Expiry Date',
                    'status' => 'Status',
                    'position' => 'Position',
                    'shares' => 'Shares',
                    'pep' => 'PEP',
                    'pep_details' => 'PEP Details',
                    'date_added' => 'Date Added',
                    'removed' => 'Removed',
                ],
            ],
        ];

        return $configs[$module] ?? null;
    }

    /**
     * Get selected columns from session with fallback to defaults
     */
    public static function getSelectedColumns($module)
    {
        $config = self::getConfig($module);
        if (!$config) {
            return [];
        }

        $columns = session($config['session_key'], $config['default_columns']);
        
        // Handle case where session value might be a JSON string
        if (is_string($columns)) {
            $decoded = json_decode($columns, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $columns = $decoded;
            } else {
                // If it's not valid JSON, use default columns
                $columns = $config['default_columns'];
            }
        }
        
        // Ensure it's always an array
        if (!is_array($columns)) {
            $columns = $config['default_columns'];
        }
        
        return $columns;
    }
}

