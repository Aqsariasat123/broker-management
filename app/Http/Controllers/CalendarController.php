<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use App\Models\Task;
use App\Models\Policy;
use App\Models\Contact;
use App\Models\Client;
use App\Models\PaymentPlan;
use App\Models\Followup;
use App\Models\LookupCategory;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CalendarController extends Controller
{
    public function index()
    {
        // Get data needed for task modal
        $taskCategory = LookupCategory::where('name', 'Task Category')->first();
        $categories = $taskCategory
            ? $taskCategory->values()->where('active', true)->orderBy('seq')->get()
            : collect([
                (object)['id' => 1, 'name' => 'General'],
                (object)['id' => 2, 'name' => 'Meeting'],
                (object)['id' => 3, 'name' => 'Follow-up'],
            ]);

        $frequencyCategories = LookupCategory::where('name', 'Frequency')->first();
        
        if (!$frequencyCategories) {
            $frequencyCategories = (object)[
                'values' => collect([
                    (object)['name' => 'Daily'],
                    (object)['name' => 'Weekly'],
                    (object)['name' => 'Monthly'],
                    (object)['name' => 'Yearly'],
                ])
            ];
        }

        $contacts = Contact::select('id', 'contact_name as name', 'contact_no')
            ->orderBy('contact_name')->get();
            
        $clients = Client::select('id', 'client_name as name', 'mobile_no as contact_no')
            ->orderBy('client_name')->get();
            
        $users = User::where('is_active', true)
            ->select('id', 'name')
            ->orderBy('name')->get();

        return view('calender.index', compact(
            'categories',
            'contacts',
            'clients',
            'users',
            'frequencyCategories'
        ));
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
        | FOLLOW UPS (from Followup model - contact follow-ups)
        |--------------------------------------------------------------------------
        */
        if ($filter === 'all' || $filter === 'follow-ups') {
            // Get follow-ups from Followup model (linked to contacts)
            $followUps = Followup::whereNotNull('follow_up_date')
                ->whereDate('follow_up_date', '>=', $viewStartDate->toDateString())
                ->whereDate('follow_up_date', '<=', $viewEndDate->toDateString())
                ->where('status', '!=', 'Completed')
                ->with(['contact', 'client'])
                ->get();

            foreach ($followUps as $followUp) {
                $dateKey = Carbon::parse($followUp->follow_up_date)->format('Y-m-d');

                // Get name from contact or client
                $name = optional($followUp->contact)->contact_name
                    ?? optional($followUp->client)->client_name
                    ?? 'Follow Up';

                $events[$dateKey][] = [
                    'text'     => $name,
                    'type'     => 'follow-up',
                    'id'       => $followUp->id,
                    'category' => 'follow-up',
                    'class'    => 'follow-up',
                ];
            }

            // Also include contacts with next_follow_up date set
            $contactFollowUps = Contact::whereNotNull('next_follow_up')
                ->whereDate('next_follow_up', '>=', $viewStartDate->toDateString())
                ->whereDate('next_follow_up', '<=', $viewEndDate->toDateString())
                ->get();

            foreach ($contactFollowUps as $contact) {
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

    /**
     * Update task - FIXED VERSION
     * Ensures JSON response for AJAX requests
     */
    public function update(Request $request, $id)
    {
        // Start output buffering to catch any unwanted output
        ob_start();
        
        try {
            Log::info('Task update request received', [
                'id' => $id,
                'data' => $request->all(),
                'is_ajax' => $request->ajax(),
                'wants_json' => $request->wantsJson(),
                'headers' => $request->headers->all()
            ]);
            
            // Find task
            $task = Task::findOrFail($id);
            
            // Validation
            $validated = $request->validate([
                'category' => 'nullable|string|max:255',
                'item' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'name' => 'nullable|string|max:255',
                'contact_no' => 'nullable|string|max:50',
                'due_date' => 'nullable|date',
                'due_time' => 'nullable',
                'date_in' => 'nullable|date',
                'assignee' => 'nullable|string|max:255',
                'task_status' => 'nullable|string|max:50',
                'date_done' => 'nullable|date',
                'task_notes' => 'nullable|string',
                'frequency' => 'nullable|string|max:50',
                'rpt_date' => 'nullable|date',
                'rpt_stop_date' => 'nullable|date',
                'repeat' => 'nullable|boolean',
            ]);
            
            // Update task
            $task->update($validated);
            
            Log::info('Task updated successfully', ['id' => $id]);
            
            // Clear any buffered output
            ob_end_clean();
            
            // ALWAYS return JSON for AJAX requests
            if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Task updated successfully',
                    'task' => $task->fresh()
                ], 200);
            }
            
            // Normal redirect for non-AJAX
            return redirect()->route('tasks.index')->with('success', 'Task updated successfully');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed', [
                'id' => $id,
                'errors' => $e->errors()
            ]);
            
            // Clear any buffered output
            ob_end_clean();
            
            if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            
            return back()->withErrors($e->errors())->withInput();
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Task not found', ['id' => $id]);
            
            // Clear any buffered output
            ob_end_clean();
            
            if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Task not found'
                ], 404);
            }
            
            return back()->with('error', 'Task not found');
            
        } catch (\Exception $e) {
            Log::error('Task update failed', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Clear any buffered output
            ob_end_clean();
            
            if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating task: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->with('error', 'Error updating task: ' . $e->getMessage());
        }
    }
}