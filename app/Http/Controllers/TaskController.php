<?php
// app/Http/Controllers/TaskController.php

namespace App\Http\Controllers;

use App\Models\Task;
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

        if ($request->has('overdue') && $request->overdue) {
            $query->where('due_date', '<', now()->format('Y-m-d'))
                  ->where('task_status', '!=', 'Completed');
        }

        // paginate 10 per page
        $tasks = $query->orderBy('due_date', 'desc')->paginate(10);

        // Get selected columns from session using TableConfigHelper
        $config = \App\Helpers\TableConfigHelper::getConfig('tasks');
        $selectedColumns = session($config['session_key'], $config['default_columns']);

        return view('tasks.index', compact('tasks', 'selectedColumns'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category' => 'required|string|max:255',
            'description' => 'required|string|max:500',
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

        $validated['task_id'] = Task::generateTaskId();
        $validated['repeat'] = $request->has('repeat');

        Task::create($validated);

        return redirect()->route('tasks.index')->with('success', 'Task created successfully.');
    }

    public function update(Request $request, Task $task): RedirectResponse
    {
        $validated = $request->validate([
            'category' => 'required|string|max:255',
            'description' => 'required|string|max:500',
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