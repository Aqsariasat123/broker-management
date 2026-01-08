<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Policy;
use App\Models\Contact;
use App\Models\Client;
use App\Models\PaymentPlan;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CalendarController extends Controller
{
    public function index()
    {
        return view('calender.index');
    }

    public function getEvents(Request $request)
    {
        $year = $request->input('year', date('Y'));
        $month = $request->input('month', date('n'));
        $filter = $request->input('filter', 'all');
        $dateRange = $request->input('date_range', 'month');

        switch ($dateRange) {
            case 'today':
                $startDate = Carbon::today();
                $endDate = Carbon::today();
                break;
            case 'week':
                $startDate = Carbon::now()->startOfWeek(); // Monday
                $endDate = Carbon::now()->endOfWeek(); // Sunday
                break;
            case 'month':
                $startDate = Carbon::create($year, $month, 1)->startOfMonth();
                $endDate = Carbon::create($year, $month, 1)->endOfMonth();
                break;
            case 'quarter':
                $quarter = floor(($month - 1) / 3) + 1;
                $startDate = Carbon::create($year)->firstDayOfQuarter()->addMonths(3 * ($quarter - 1));
                $endDate = $startDate->copy()->addMonths(3)->subDay();
                break;
            case 'year':
                $startDate = Carbon::create($year)->startOfYear();
                $endDate = Carbon::create($year)->endOfYear();
                break;
            default:
                if (str_starts_with($dateRange, 'year-')) {
                    $yearOnly = (int) str_replace('year-', '', $dateRange);
                    $startDate = Carbon::create($yearOnly)->startOfYear();
                    $endDate = Carbon::create($yearOnly)->endOfYear();
                } else {
                    $startDate = Carbon::create($year, $month, 1)->startOfMonth();
                    $endDate = Carbon::create($year, $month, 1)->endOfMonth();
                }
                break;
       }

        // Include previous and next month days that are visible in calendar
        // $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        // $endDate = $startDate->copy()->endOfMonth();
        
        // Get first day of week (Monday = 0)
        $firstDayOfWeek = $startDate->dayOfWeek; // 0 = Sunday, 1 = Monday, etc.
        $firstDayOfWeek = $firstDayOfWeek === 0 ? 6 : $firstDayOfWeek - 1; // Convert to Monday = 0
        
        // Get last day of week (Sunday = 6)
        $lastDayOfWeek = $endDate->dayOfWeek;
        $lastDayOfWeek = $lastDayOfWeek === 0 ? 6 : $lastDayOfWeek - 1;
        
        // Extend start date to include previous month days
        $viewStartDate = $startDate->copy()->subDays($firstDayOfWeek);
        // Extend end date to include next month days (to fill 6 weeks = 42 days)
        $viewEndDate = $viewStartDate->copy()->addDays(41);

        $events = [];

        // Tasks
        if ($filter === 'all' || $filter === 'tasks') {
            $tasks = Task::whereBetween('due_date', [$viewStartDate, $viewEndDate])
                ->where('task_status', '!=', 'Completed')
                ->get();
            
            foreach ($tasks as $task) {
                $dateKey = $task->due_date->format('Y-m-d');
                if (!isset($events[$dateKey])) {
                    $events[$dateKey] = [];
                }
                $events[$dateKey][] = [
                    'text' => $task->item ?: $task->description,
                    'type' => 'task',
                    'id' => $task->id,
                    'category' => 'task',
                    'class' => 'task'
                ];
            }
        }

        // Follow Ups (from Contacts)
        if ($filter === 'all' || $filter === 'follow-ups') {
            $followUps = Contact::whereNotNull('next_follow_up')
                ->whereBetween('next_follow_up', [$viewStartDate, $viewEndDate])
                ->get();
            
            foreach ($followUps as $contact) {
                $dateKey = $contact->next_follow_up->format('Y-m-d');
                if (!isset($events[$dateKey])) {
                    $events[$dateKey] = [];
                }
                $events[$dateKey][] = [
                    'text' => $contact->contact_name,
                    'type' => 'follow-up',
                    'id' => $contact->id,
                    'category' => 'follow-up',
                    'class' => 'follow-up'
                ];
            }
        }

        // Renewals (Policies due for renewal)
        if ($filter === 'all' || $filter === 'renewals') {
            $renewals = Policy::whereNotNull('end_date')
                ->whereBetween('end_date', [$viewStartDate, $viewEndDate])
                ->where('renewable', true)
                ->with('client')
                ->get();
            
            foreach ($renewals as $policy) {
                $dateKey = $policy->end_date->format('Y-m-d');
                if (!isset($events[$dateKey])) {
                    $events[$dateKey] = [];
                }
                $clientName = $policy->client ? $policy->client->client_name : ($policy->client_name ?? 'Unknown');
                $events[$dateKey][] = [
                    'text' => $clientName,
                    'type' => 'renewal',
                    'id' => $policy->id,
                    'category' => 'renewal',
                    'class' => 'renewal'
                ];
            }
        }

        // Instalments (Payment Plans)
        if ($filter === 'all' || $filter === 'instalments') {
            $instalments = PaymentPlan::whereBetween('due_date', [$viewStartDate, $viewEndDate])
                ->where('status', '!=', 'paid')
                ->with('schedule.policy.client')
                ->get();
            
            foreach ($instalments as $plan) {
                $dateKey = $plan->due_date->format('Y-m-d');
                if (!isset($events[$dateKey])) {
                    $events[$dateKey] = [];
                }
                $clientName = 'Unknown';
                if ($plan->schedule && $plan->schedule->policy && $plan->schedule->policy->client) {
                    $clientName = $plan->schedule->policy->client->client_name;
                }
                $events[$dateKey][] = [
                    'text' => $clientName . ' - ' . ($plan->installment_label ?? 'Instalment'),
                    'type' => 'instalment',
                    'id' => $plan->id,
                    'category' => 'instalment',
                    'class' => 'instalment'
                ];
            }
        }

        // Birthdays (from Clients and Contacts)
        if ($filter === 'all' || $filter === 'birthdays') {
            // Get birthdays from Clients
            $clientBirthdays = Client::whereNotNull('dob_dor')->get();
            
            foreach ($clientBirthdays as $client) {
                if (!$client->dob_dor) continue;
                
                // Check each year that might be visible in the calendar view
                $checkYear = $viewStartDate->year;
                $birthdayThisYear = Carbon::create($checkYear, $client->dob_dor->month, $client->dob_dor->day);
                
                // If birthday is before view start, check next year
                if ($birthdayThisYear->lt($viewStartDate)) {
                    $birthdayThisYear->addYear();
                }
                
                // If birthday falls within view range, add it
                if ($birthdayThisYear->between($viewStartDate, $viewEndDate)) {
                    $dateKey = $birthdayThisYear->format('Y-m-d');
                    if (!isset($events[$dateKey])) {
                        $events[$dateKey] = [];
                    }
                    $events[$dateKey][] = [
                        'text' => $client->client_name,
                        'type' => 'birthday',
                        'id' => $client->id,
                        'category' => 'birthday',
                        'class' => 'birthday'
                    ];
                }
            }

            // Get birthdays from Contacts
            $contactBirthdays = Contact::whereNotNull('dob')->get();
            
            foreach ($contactBirthdays as $contact) {
                if (!$contact->dob) continue;
                
                // Check each year that might be visible in the calendar view
                $checkYear = $viewStartDate->year;
                $birthdayThisYear = Carbon::create($checkYear, $contact->dob->month, $contact->dob->day);
                
                // If birthday is before view start, check next year
                if ($birthdayThisYear->lt($viewStartDate)) {
                    $birthdayThisYear->addYear();
                }
                
                // If birthday falls within view range, add it
                if ($birthdayThisYear->between($viewStartDate, $viewEndDate)) {
                    $dateKey = $birthdayThisYear->format('Y-m-d');
                    if (!isset($events[$dateKey])) {
                        $events[$dateKey] = [];
                    }
                    $events[$dateKey][] = [
                        'text' => $contact->contact_name,
                        'type' => 'birthday',
                        'id' => $contact->id,
                        'category' => 'birthday',
                        'class' => 'birthday'
                    ];
                }
            }
        }

        return response()->json($events);
    }
}

