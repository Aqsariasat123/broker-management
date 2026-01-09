<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Log;

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
        $year      = (int) $request->input('year', date('Y'));
        $month     = (int) $request->input('month', date('n'));
        $filter    = $request->input('filter', 'all');
        $dateRange = $request->input('date_range', 'month');

        /*
        |--------------------------------------------------------------------------
        | Base Date Range
        |--------------------------------------------------------------------------
        */
        switch ($dateRange) {
            case 'today':
                $startDate = Carbon::today();
                $endDate   = Carbon::today();
                break;

            case 'week':
                $startDate = Carbon::now()->startOfWeek(); // Monday
                $endDate   = Carbon::now()->endOfWeek();   // Sunday
                break;

            case 'month':
                $startDate = Carbon::create($year, $month, 1)->startOfMonth();
                $endDate   = Carbon::create($year, $month, 1)->endOfMonth();
                break;

            case 'quarter':
                $quarter   = floor(($month - 1) / 3) + 1;
                $startDate = Carbon::create($year)->firstDayOfQuarter()->addMonths(3 * ($quarter - 1));
                $endDate   = $startDate->copy()->addMonths(3)->subDay();
                break;

            case 'year':
                $startDate = Carbon::create($year)->startOfYear();
                $endDate   = Carbon::create($year)->endOfYear();
                break;

            default:
                // Handles: year-2025
                if (str_starts_with($dateRange, 'year-')) {
                    $yearOnly  = (int) str_replace('year-', '', $dateRange);
                    $startDate = Carbon::create($yearOnly)->startOfYear();
                    $endDate   = Carbon::create($yearOnly)->endOfYear();
                } else {
                    $startDate = Carbon::create($year, $month, 1)->startOfMonth();
                    $endDate   = Carbon::create($year, $month, 1)->endOfMonth();
                }
                break;
        }

        /*
        |--------------------------------------------------------------------------
        | Extend Range to Fill Calendar Grid (6 weeks / 42 days)
        |--------------------------------------------------------------------------
        */
        // Convert Sunday(0) → Monday(0)
        $firstDayOffset = $startDate->dayOfWeek === 0
            ? 6
            : $startDate->dayOfWeek - 1;

        $viewStartDate = $startDate->copy()->subDays($firstDayOffset)->startOfDay();
        $viewEndDate   = $viewStartDate->copy()->addDays(41)->endOfDay();

        Log::info('Calendar view range', [
            'viewStartDate' => $viewStartDate->toDateTimeString(),
            'viewEndDate'   => $viewEndDate->toDateTimeString(),
        ]);

        $events = [];

        /*
        |--------------------------------------------------------------------------
        | TASKS
        |--------------------------------------------------------------------------
        */
        if ($filter === 'all' || $filter === 'tasks') {
            $tasks = Task::where('task_status', '!=', 'Completed')
                ->whereDate('due_date', '>=', $viewStartDate->toDateString())
                ->whereDate('due_date', '<=', $viewEndDate->toDateString())
                ->get();


            foreach ($tasks as $task) {
                if (!$task->due_date) continue;

                $dateKey = Carbon::parse($task->due_date)->format('Y-m-d');

                $events[$dateKey][] = [
                    'text'     => $task->item ?: $task->description,
                    'type'     => 'task',
                    'id'       => $task->id,
                    'category' => 'task',
                    'class'    => 'task',
                ];
            }
        }

        /*
        |--------------------------------------------------------------------------
        | FOLLOW UPS
        |--------------------------------------------------------------------------
        */
        if ($filter === 'all' || $filter === 'follow-ups') {
            $followUps = Contact::whereNotNull('next_follow_up')
                ->whereDate('next_follow_up', '>=', $viewStartDate->toDateString())
                ->whereDate('next_follow_up', '<=', $viewEndDate->toDateString())
                ->get();

            foreach ($followUps as $contact) {
                $dateKey = Carbon::parse($contact->next_follow_up)->format('Y-m-d');

                $events[$dateKey][] = [
                    'text'     => $contact->contact_name,
                    'type'     => 'follow-up',
                    'id'       => $contact->id,
                    'category' => 'follow-up',
                    'class'    => 'follow-up',
                ];
            }
        }

        /*
        |--------------------------------------------------------------------------
        | RENEWALS
        |--------------------------------------------------------------------------
        */
        if ($filter === 'all' || $filter === 'renewals') {
            $renewals = Policy::whereNotNull('end_date')
                ->whereDate('end_date', '>=', $viewStartDate->toDateString())
                ->whereDate('end_date', '<=', $viewEndDate->toDateString())
                ->where('renewable', true)
                ->with('client')
                ->get();

            foreach ($renewals as $policy) {
                $dateKey = Carbon::parse($policy->end_date)->format('Y-m-d');

                $events[$dateKey][] = [
                    'text'     => optional($policy->client)->client_name ?? 'Unknown',
                    'type'     => 'renewal',
                    'id'       => $policy->id,
                    'category' => 'renewal',
                    'class'    => 'renewal',
                ];
            }
        }

        /*
        |--------------------------------------------------------------------------
        | INSTALMENTS
        |--------------------------------------------------------------------------
        */
        if ($filter === 'all' || $filter === 'instalments') {
            $instalments = PaymentPlan::where('status', '!=', 'paid')
                ->whereDate('due_date', '>=', $viewStartDate->toDateString())
                ->whereDate('due_date', '<=', $viewEndDate->toDateString())
                ->with('schedule.policy.client')
                ->get();

            foreach ($instalments as $plan) {
                $dateKey = Carbon::parse($plan->due_date)->format('Y-m-d');

                $clientName = optional($plan->schedule?->policy?->client)->client_name ?? 'Unknown';

                $events[$dateKey][] = [
                    'text'     => $clientName . ' - ' . ($plan->installment_label ?? 'Instalment'),
                    'type'     => 'instalment',
                    'id'       => $plan->id,
                    'category' => 'instalment',
                    'class'    => 'instalment',
                ];
            }
        }

        /*
        |--------------------------------------------------------------------------
        | BIRTHDAYS (Clients + Contacts)
        |--------------------------------------------------------------------------
        */
        if ($filter === 'all' || $filter === 'birthdays') {

            foreach (Client::whereNotNull('dob_dor')->get() as $client) {
                $birthday = Carbon::create($viewStartDate->year, $client->dob_dor->month, $client->dob_dor->day);
                if ($birthday->lt($viewStartDate)) $birthday->addYear();

                if ($birthday->between($viewStartDate, $viewEndDate)) {
                    $events[$birthday->format('Y-m-d')][] = [
                        'text'     => $client->client_name,
                        'type'     => 'birthday',
                        'id'       => $client->id,
                        'category' => 'birthday',
                        'class'    => 'birthday',
                    ];
                }
            }

            foreach (Contact::whereNotNull('dob')->get() as $contact) {
                $birthday = Carbon::create($viewStartDate->year, $contact->dob->month, $contact->dob->day);
                if ($birthday->lt($viewStartDate)) $birthday->addYear();

                if ($birthday->between($viewStartDate, $viewEndDate)) {
                    $events[$birthday->format('Y-m-d')][] = [
                        'text'     => $contact->contact_name,
                        'type'     => 'birthday',
                        'id'       => $contact->id,
                        'category' => 'birthday',
                        'class'    => 'birthday',
                    ];
                }
            }
        }

        Log::info('Final calendar events count', ['days' => count($events)]);

        return response()->json($events);
    }

}

