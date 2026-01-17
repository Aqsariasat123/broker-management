<?php
// app/Http/Controllers/TaskController.php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Log; // <-- Add this

use App\Models\Task;
use App\Models\LookupCategory;
use App\Models\LookupValue;
use App\Models\Contact;
use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\Response;
use Carbon\Carbon;

class TaskController extends Controller
{

    public function index(Request $request): View
{
    $query = Task::query();

    // Apply task-specific filters
    if ($request->has('filter') && $request->filter == 'overdue') {
        $query->where('due_date', '<', now()->format('Y-m-d'))
            ->where('task_status', '!=', 'Completed');
    }

    // ✅ Date filtering - ONLY when explicitly requested
    $startDate = null;
    $endDate = null;

    // Priority 1: Calendar dates (specific dates from calendar)
    if ($request->has('from_calendar') && $request->has('start_date') && $request->has('end_date')) {
        $startDate = Carbon::parse($request->get('start_date'))->startOfDay();
        $endDate = Carbon::parse($request->get('end_date'))->endOfDay();
    }
    // Priority 2: Date range filter (only if explicitly set)
    elseif ($request->has('date_range') && $request->date_range) {
        $dateRange = $request->date_range; // No default value
        $now = Carbon::now();

        switch ($dateRange) {
            case 'today':
                $startDate = $now->copy()->startOfDay();
                $endDate = $now->copy()->endOfDay();
                break;

            case 'week':
                $startDate = $now->copy()->startOfWeek();
                $endDate = $now->copy()->endOfWeek();
                break;

            case 'month':
                $startDate = $now->copy()->startOfMonth();
                $endDate = $now->copy()->endOfMonth();
                break;

            case 'quarter':
                $startDate = $now->copy()->firstOfQuarter();
                $endDate = $now->copy()->lastOfQuarter();
                break;

            case 'year':
                $startDate = $now->copy()->startOfYear();
                $endDate = $now->copy()->endOfYear();
                break;

            default:
                if (str_starts_with($dateRange, 'year-')) {
                    $selectedYear = (int) str_replace('year-', '', $dateRange);
                    $startDate = Carbon::create($selectedYear, 1, 1)->startOfDay();
                    $endDate = Carbon::create($selectedYear, 12, 31)->endOfDay();
                }
                break;
        }
    }
    // No else - if neither calendar nor date_range, show ALL tasks

    // Apply date filter only if dates are set
    if ($startDate && $endDate) {
        $query->whereBetween('due_date', [$startDate, $endDate]);
    }

    // Paginate and eager load relations
    $tasks = $query->with([
        'categoryValues',
        'assigneeUser',
        'contact',
        'client'
    ])->orderBy('due_date', 'desc')->paginate(15); // Increased to 15

    // Get selected columns
    $config = \App\Helpers\TableConfigHelper::getConfig('tasks');
    $selectedColumns = session($config['session_key'], $config['default_columns']);

    // Fetch dropdown data
    $taskCategory = LookupCategory::where('name', 'Task Category')->first();
    $categories = $taskCategory
        ? $taskCategory->values()->where('active', true)->orderBy('seq')->get()
        : collect();

    $frequencyCategories = LookupCategory::where('name', 'Frequency')->first();

    // Contacts and clients
    $contacts = Contact::select('id', 'contact_name as name', 'contact_no')
        ->orderBy('contact_name')->get();
    $clients = Client::select('id', 'client_name as name', 'mobile_no as contact_no')
        ->orderBy('client_name')->get();

    // Users
    $users = User::where('is_active', true)->select('id', 'name')->orderBy('name')->get();

    return view('tasks.index', compact(
        'tasks',
        'selectedColumns',
        'categories',
        'frequencyCategories',
        'contacts',
        'clients',
        'users'
    ));
}

        // public function index(Request $request): View
        // {
        //     $query = Task::query();

        //     // Apply task-specific filters
        //     if ($request->has('filter') && $request->filter == 'overdue') {
        //         $query->where('due_date', '<', now()->format('Y-m-d'))
        //             ->where('task_status', '!=', 'Completed');
        //     }

        //     // Handle date filtering - from calendar or date_range
        //     $startDate = null;
        //     $endDate = null;

        //     // If coming from calendar with specific dates
        //     if ($request->has('from_calendar') && $request->has('start_date') && $request->has('end_date')) {
        //         $startDate = Carbon::parse($request->get('start_date'))->startOfDay();
        //         $endDate = Carbon::parse($request->get('end_date'))->endOfDay();
        //     } else {
        //         // Handle date range filter
        //         $dateRange = $request->get('date_range', 'month'); // default = 'month'
        //         $now = Carbon::now();

        //         switch ($dateRange) {
        //             case 'today':
        //                 $startDate = $now->copy()->startOfDay();
        //                 $endDate = $now->copy()->endOfDay();
        //                 break;

        //             case 'week':
        //                 $startDate = $now->copy()->startOfWeek();
        //                 $endDate = $now->copy()->endOfWeek();
        //                 break;

        //             case 'month':
        //                 $startDate = $now->copy()->startOfMonth();
        //                 $endDate = $now->copy()->endOfMonth();
        //                 break;

        //             case 'quarter':
        //                 $startDate = $now->copy()->firstOfQuarter();
        //                 $endDate = $now->copy()->lastOfQuarter();
        //                 break;

        //             case 'year':
        //                 $startDate = $now->copy()->startOfYear();
        //                 $endDate = $now->copy()->endOfYear();
        //                 break;

        //             default:
        //                 if (str_starts_with($dateRange, 'year-')) {
        //                     $selectedYear = (int) str_replace('year-', '', $dateRange);
        //                     $startDate = Carbon::create($selectedYear, 1, 1)->startOfDay();
        //                     $endDate = Carbon::create($selectedYear, 12, 31)->endOfDay();
        //                 }
        //                 break;
        //         }
        //     }

        //     if ($startDate && $endDate) {
        //         $query->whereBetween('due_date', [$startDate, $endDate]);
        //     }

        //     // Paginate and eager load relations
        //     $tasks = $query->with([
        //         'categoryValues',  // Task Category
        //         'assigneeUser',    // User
        //         'contact',         // Contact
        //         'client'           // Client
        //     ])->orderBy('due_date', 'desc')->paginate(10);

        //     // Get selected columns from session using TableConfigHelper
        //     $config = \App\Helpers\TableConfigHelper::getConfig('tasks');
        //     $selectedColumns = session($config['session_key'], $config['default_columns']);

        //     // Fetch dropdown data
        //     $taskCategory = LookupCategory::where('name', 'Task Category')->first();
        //     $categories = $taskCategory
        //         ? $taskCategory->values()->where('active', true)->orderBy('seq')->get()
        //         : collect();

        //     $frequencyCategories = LookupCategory::where('name', 'Frequency')->first();

        //     // Contacts and clients for dropdown
        //     $contacts = Contact::select('id', 'contact_name as name', 'contact_no')
        //         ->orderBy('contact_name')->get();
        //     $clients = Client::select('id', 'client_name as name', 'mobile_no as contact_no')
        //         ->orderBy('client_name')->get();

        //     // Users for assignee dropdown
        //     $users = User::where('is_active', true)->select('id', 'name')->orderBy('name')->get();

        //     Log::info('Selected Columns: ', $tasks->toArray());

        //     return view('tasks.index', compact(
        //         'tasks',
        //         'selectedColumns',
        //         'categories',
        //         'frequencyCategories',
        //         'contacts',
        //         'clients',
        //         'users'
        //     ));
        // }


    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category' => 'required|string|max:255',
            'item' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
            'name' => 'required|string|max:255',
            'contact_no' => 'nullable|string|max:20',
            'due_date' => 'required|date',
            'due_time' => 'nullable|date_format:H:i',
            'date_in' => 'nullable|date',
            'assignee' => 'required|string|max:255',
            'task_status' => 'required|in:Not Done,In Progress,Completed',
            'date_done' => 'nullable|date',
            'repeat' => 'boolean',
            'frequency' => 'nullable|string|max:255',
            'rpt_date' => 'nullable|date',
            'rpt_stop_date' => 'nullable|date',
            'task_notes' => 'nullable|string'
        ]);

        // Set description from item if not provided
        if (empty($validated['description'])) {
            $validated['description'] = $validated['item'] ?? $validated['category'];
        }

        $validated['task_id'] = Task::generateTaskId();
        $validated['repeat'] = $request->has('repeat');

        Task::create($validated);

        return redirect()->route('tasks.index')->with('success', 'Task created successfully.');
    }

    public function update(Request $request, Task $task): RedirectResponse
    {
        $validated = $request->validate([
            'category' => 'required|string|max:255',
            'item' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
            'name' => 'required|string|max:255',
            'contact_no' => 'nullable|string|max:20',
            'due_date' => 'required|date',
            'due_time' => 'nullable|date_format:H:i',
            'date_in' => 'nullable|date',
            'assignee' => 'required|string|max:255',
            'task_status' => 'required|in:Not Done,In Progress,Completed',
            'date_done' => 'nullable|date',
            'repeat' => 'boolean',
            'frequency' => 'nullable|string|max:255',
            'rpt_date' => 'nullable|date',
            'rpt_stop_date' => 'nullable|date',
            'task_notes' => 'nullable|string'
        ]);

        // Set description from item if not provided
        if (empty($validated['description'])) {
            $validated['description'] = $validated['item'] ?? $validated['category'];
        }

        $validated['repeat'] = $request->has('repeat');

        $task->update($validated);

        return redirect()->route('tasks.index')->with('success', 'Task updated successfully.');
    }

    public function destroy(Task $task): RedirectResponse
    {
        $task->delete();
        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully.');
    }

    public function getTask(Task $task)
    {
        return response()->json($task);
    }

    public function show(Task $task)
    {
        if (request()->expectsJson()) {
            return response()->json($task);
        }
        return view('tasks.show', compact('task'));
    }

    public function edit(Task $task)
    {
        if (request()->expectsJson()) {
            return response()->json($task);
        }
        return view('tasks.edit', compact('task'));
    }

    public function saveColumnSettings(Request $request): RedirectResponse
    {
        $selectedColumns = $request->input('columns', []);
        $config = \App\Helpers\TableConfigHelper::getConfig('tasks');
        session([$config['session_key'] => $selectedColumns]);

        return redirect()->route('tasks.index')->with('success', 'Column settings saved successfully.');
    }

    // Export current page records as CSV (opens in Excel)
    public function export(Request $request)
    {
        $page = (int) $request->get('page', 1);

        $query = Task::query();
        if ($request->has('overdue') && $request->overdue) {
            $query->where('due_date', '<', now()->format('Y-m-d'))
                  ->where('task_status', '!=', 'Completed');
        }

        $config = \App\Helpers\TableConfigHelper::getConfig('tasks');
        $selectedColumns = session($config['session_key'], $config['default_columns']);

        // get paginator for the requested page (10 per page)
        $paginator = $query->orderBy('due_date', 'desc')->paginate(10, ['*'], 'page', $page);
        $tasks = $paginator->items(); // array of Task models

        $filename = "tasks_page_{$page}.csv";
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($tasks, $selectedColumns) {
            $out = fopen('php://output', 'w');
            // add UTF-8 BOM so Excel opens CSV with correct encoding
            fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));

            // header row (human readable names)
            $headerRow = array_map(function($c){
                // simple title mapping (could be improved)
                return ucwords(str_replace('_',' ',$c));
            }, $selectedColumns);
            fputcsv($out, $headerRow);

            foreach ($tasks as $task) {
                $row = [];
                foreach ($selectedColumns as $col) {
                    $row[] = $this->formatExportValue($task, $col);
                }
                fputcsv($out, $row);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function formatExportValue(Task $task, string $col)
    {
        $val = null;
        switch ($col) {
            case 'due_date':
            case 'date_in':
            case 'date_done':
            case 'rpt_date':
            case 'rpt_stop_date':
                $raw = $task->{$col} ?? null;
                if ($raw) {
                    try {
                        return Carbon::parse($raw)->format('d-M-y');
                    } catch (\Exception $e) {
                        return (string)$raw;
                    }
                }
                return '';
            case 'due_in':
                $dueIn = $task->getDueInDays();
                return $dueIn !== null ? (string)$dueIn : '';
            case 'repeat':
                return $task->repeat ? 'Y' : 'N';
            default:
                $v = $task->{$col} ?? '';
                return is_null($v) ? '' : (string)$v;
        }
    }

    private function getDefaultColumns()
    {
        return [
            'task_id', 'category', 'description', 'name', 'contact_no',
            'due_date', 'due_time', 'date_in', 'assignee', 'task_status',
            'date_done', 'repeat', 'frequency', 'rpt_date', 'rpt_stop_date'
        ];
    }
}