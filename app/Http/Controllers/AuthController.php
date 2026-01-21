<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use App\Models\Policy;
use App\Models\Client;
use App\Models\Task;
use App\Models\PaymentPlan;
use App\Models\Payment;
use App\Models\DebitNote;
use App\Models\Income;
use App\Models\Expense;
use App\Models\Contact;
use App\Models\LifeProposal;
use App\Models\Followup;
use App\Models\AuditLog;
use App\Models\Commission;
use Carbon\Carbon;

class AuthController extends Controller
{
    // Show login page
    public function showLoginForm()
    {
        return view('login');
    }

    // Handle login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'name' => 'required',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            
            // Check if user is active
            if (!$user->is_active) {
                Auth::logout();
                return back()->withErrors([
                    'name' => 'Your account has been deactivated. Please contact administrator.',
                ])->withInput($request->only('name'));
            }

            // Update last login info
            $user->update([
                'last_login_at' => now(),
                'last_login_ip' => $request->ip(),
            ]);

            // Log login activity
            AuditLog::log('login', null, null, null, 'User logged in');

            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        // Log failed login attempt
        AuditLog::log('login_failed', null, null, null, 'Failed login attempt for: ' . $request->name);

        return back()->withErrors([
            'name' => 'Invalid Credentials Provided!',
        ])->withInput($request->only('name'));
    }


        public function dashboard(Request $request)
        {

            // -------------------------------
            // 1. Get selected date range & year
            // -------------------------------
            $dateRange = $request->get('date_range', 'month'); // null = default filter
            $selectedYear = now()->year; // default

          if ($dateRange === 'year') {
                $selectedYear = now()->year;
            } elseif (str_starts_with($dateRange, 'year-')) {
                $selectedYear = (int) str_replace('year-', '', $dateRange);
            }

            $incomeExpenseYear = (int) $request->get('incomeExpenseYear', $selectedYear);
            $incomeYear = (int) $request->get('incomeYear', $selectedYear);
            $expenseYear = (int) $request->get('expenseYear', $selectedYear);

            $today = now()->startOfDay();

            // -------------------------------
            // 2. AJAX chart updates
            // -------------------------------
            if ($request->expectsJson() || $request->wantsJson()) {
                $chartType = null;
                $year = null;

                if ($request->has('incomeExpenseYear')) {
                    $chartType = 'incomeExpense';
                    $year = $incomeExpenseYear;
                } elseif ($request->has('incomeYear')) {
                    $chartType = 'income';
                    $year = $incomeYear;
                } elseif ($request->has('expenseYear')) {
                    $chartType = 'expense';
                    $year = $expenseYear;
                }

                if ($chartType) {
                    return $this->getChartData($year, $chartType);
                }
            }

            // -------------------------------
            // 3. Determine start/end dates
            // -------------------------------
            switch ($dateRange) {
                case 'today':
                    $startDate = $today;
                    $endDate = $today->copy()->endOfDay();
                    break;
                case 'week':
                    $startDate = $today->copy()->startOfWeek();
                    $endDate = $today->copy()->endOfWeek();
                    break;
                case 'month':
                    $startDate = $today->copy()->startOfMonth();
                    $endDate = $today->copy()->endOfMonth();
                    break;
                case 'quarter':
                    $startDate = $today->copy()->startOfQuarter();
                    $endDate = $today->copy()->endOfQuarter();
                    break;
                case 'year':
                case null:
                default:
                    $startDate = Carbon::create($selectedYear, 1, 1)->startOfDay();
                    $endDate = Carbon::create($selectedYear, 12, 31)->endOfDay();
                    break;
            }

           
            $stats = [
                // Tasks due today or overdue (not completed)
                'tasks_today' => Task::query()
                    ->where('task_status', '!=', 'Completed')
                    ->where('due_date', '<=', now()->format('Y-m-d'))
                    ->count(),

                // Policies expiring in selected date range
                'policies_expiring' => Policy::query()
                    ->whereBetween('end_date', [$startDate, $endDate])
                    ->where('policy_status', '!=', 'Cancelled')
                    ->count(),

                // Instalments overdue (unpaid and past due date)
                'instalments_overdue' => PaymentPlan::query()
                    ->where('status', '!=', 'Paid')
                    ->where('due_date', '<', now()->format('Y-m-d'))
                    ->count(),

                // IDs expired (clients with expired ID documents)
                'ids_expired' => Client::query()
                    ->whereNotNull('id_expiry_date')
                    ->where('id_expiry_date', '<', now()->format('Y-m-d'))
                    ->count(),

                // General policies (non-life policies) count
                'general_policies' => Policy::whereHas('policyClass', function($q) {
                        $q->where('name', 'not like', '%Life%');
                    })->count(),

                // Gen-Com Outstanding - unpaid commission amounts
                'gen_com_outstanding' => Commission::query()
                    ->whereHas('paymentStatus', fn($q) => $q->where('name', '!=', 'Paid'))
                    ->sum('amount_due') ?? 0,

                // Open leads (contacts not archived or converted)
                'open_leads' => Contact::query()
                    ->whereNotIn('status', ['Archived', 'Converted', 'Lost'])
                    ->count(),

                // Follow-ups due today or overdue
                'follow_ups_today' => Followup::query()
                    ->where('status', '!=', 'Completed')
                    ->where('follow_up_date', '<=', now()->format('Y-m-d'))
                    ->count(),

                // Life Proposals with Pending status
                'proposals_pending' => LifeProposal::query()
                    ->whereHas('status', fn($q) => $q->where('name', 'like', '%Pending%'))
                    ->count(),

                // Life Proposals with Processing status
                'proposals_processing' => LifeProposal::query()
                    ->whereHas('status', fn($q) => $q->where('name', 'like', '%Processing%'))
                    ->count(),

                // Life policies count
                'life_policies' => Policy::whereHas('policyClass', function($q) {
                        $q->where('name', 'like', '%Life%');
                    })->count(),

                // Birthdays today (matching month and day)
                'birthdays_today' => Client::query()
                    ->whereNotNull('dob_dor')
                    ->whereMonth('dob_dor', now()->month)
                    ->whereDay('dob_dor', now()->day)
                    ->count(),
            ];


            // -------------------------------
            // 6. Monthly Chart Data
            // -------------------------------
            $monthlyData = $this->calculateMonthlyData($selectedYear);
            $incomeExpenseMonthlyData = $this->calculateMonthlyData($incomeExpenseYear);
            $incomeMonthlyData = $this->calculateMonthlyData($incomeYear);
            $expenseMonthlyData = $this->calculateMonthlyData($expenseYear);

            $totalIncome = Income::whereYear('date_received', $selectedYear)->sum('amount_received');
            $totalExpense = Expense::whereYear('date_paid', $selectedYear)->sum('amount_paid');

            $incomeExpenseTotalIncome = Income::whereYear('date_received', $incomeExpenseYear)->sum('amount_received');
            $incomeExpenseTotalExpense = Expense::whereYear('date_paid', $incomeExpenseYear)->sum('amount_paid');

            $yearStart = Carbon::create($selectedYear, 1, 1);
            $yearEnd = Carbon::create($selectedYear, 12, 31);

            // -------------------------------
            // 7. Recent Activities
            // -------------------------------
            $recentPolicies = Policy::with('client')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            $recentPayments = Payment::with(['debitNote.paymentPlan.schedule.policy.client'])
                ->whereBetween('paid_on', [$startDate, $endDate])
                ->orderBy('paid_on', 'desc')
                ->limit(5)
                ->get();

            // -------------------------------
            // 8. Return view
            // -------------------------------
            return view('dashboard', compact(
                'stats', 'monthlyData',
                'incomeExpenseMonthlyData','incomeExpenseTotalIncome','incomeExpenseTotalExpense',
                'incomeMonthlyData','expenseMonthlyData',
                'totalIncome','totalExpense','yearStart','yearEnd',
                'incomeExpenseYear','incomeYear','expenseYear',
                'selectedYear','dateRange','recentPolicies','recentPayments',
                
            ))->with('selectedDateRange', $dateRange);
        }




    private function calculateMonthlyData($year)
    {
        $monthlyData = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::create($year, 1, 1)->addMonths($i);
            $income = Income::whereMonth('date_received', $month->month)
                ->whereYear('date_received', $month->year)
                ->sum('amount_received');
            $expense = Expense::whereMonth('date_paid', $month->month)
                ->whereYear('date_paid', $month->year)
                ->sum('amount_paid');
            
            // Calculate sells count (policies created in that month)
            $sells = Policy::whereMonth('date_registered', $month->month)
                ->whereYear('date_registered', $month->year)
                ->count();
            
            $monthlyData[] = [
                'month' => $month->format('F'),
                'month_short' => $month->format('M'),
                'income' => $income,
                'expense' => $expense,
                'sells' => $sells,
            ];
        }
        
        // Calculate percentages for each month (based on max value)
        $maxIncome = $monthlyData ? max(array_column($monthlyData, 'income')) : 1;
        $maxExpense = $monthlyData ? max(array_column($monthlyData, 'expense')) : 1;
        foreach ($monthlyData as &$data) {
            $data['income_percent'] = $maxIncome > 0 ? round(($data['income'] / $maxIncome) * 100) : 0;
            $data['expense_percent'] = $maxExpense > 0 ? round(($data['expense'] / $maxExpense) * 100) : 0;
        }
        
        return $monthlyData;
    }
    
    private function getChartData($year, $chartType)
    {
        $monthlyData = $this->calculateMonthlyData($year);
        $yearStart = Carbon::create($year, 1, 1);
        $yearEnd = Carbon::create($year, 12, 31);
        
        $response = [
            'monthlyData' => $monthlyData,
            'yearStart' => $yearStart->format('j-M-y'),
            'yearEnd' => $yearEnd->format('j-M-y'),
        ];
        
        if ($chartType === 'incomeExpense') {
            $response['totalIncome'] = Income::whereYear('date_received', $year)->sum('amount_received');
            $response['totalExpense'] = Expense::whereYear('date_paid', $year)->sum('amount_paid');
        }
        
        return response()->json($response);
    }

    // Export Dashboard Report
    public function exportDashboard(Request $request)
    {
        $dateRange = $request->get('date_range', 'month');
        $today = now()->startOfDay();
        
        // Set date range based on selection
        switch ($dateRange) {
            case 'today':
                $startDate = $today;
                $endDate = $today->copy()->endOfDay();
                break;
            case 'week':
                $startDate = $today->copy()->startOfWeek();
                $endDate = $today->copy()->endOfWeek();
                break;
            case 'quarter':
                $startDate = $today->copy()->startOfQuarter();
                $endDate = $today->copy()->endOfQuarter();
                break;
            case 'year':
                $startDate = $today->copy()->startOfYear();
                $endDate = $today->copy()->endOfYear();
                break;
            default: // month
                $startDate = $today->copy()->startOfMonth();
                $endDate = $today->copy()->endOfMonth();
        }

        $fileName = 'dashboard_report_' . $dateRange . '_' . now()->format('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        $handle = fopen('php://output', 'w');
        fputcsv($handle, ['Dashboard Report - ' . ucfirst($dateRange)]);
        fputcsv($handle, ['Generated: ' . now()->format('Y-m-d H:i:s')]);
        fputcsv($handle, []);

        // Statistics
        fputcsv($handle, ['Statistics']);
        fputcsv($handle, ['Metric', 'Value']);
        fputcsv($handle, ['Tasks Today', Task::whereDate('due_date', $today)->where('task_status', '!=', 'Completed')->count()]);
        fputcsv($handle, ['Policies Expiring (30 days)', Policy::whereBetween('end_date', [$today, $today->copy()->addDays(30)])->count()]);
        fputcsv($handle, ['Instalments Overdue', PaymentPlan::where('due_date', '<', $today)->where('status', '!=', 'paid')->count()]);
        fputcsv($handle, ['Total Policies', Policy::count()]);
        fputcsv($handle, ['Outstanding Amount', PaymentPlan::where('status', '!=', 'paid')->sum('amount')]);
        fputcsv($handle, ['Open Leads', Contact::where('status', '!=', 'Archived')->count()]);
        fputcsv($handle, []);

        // Policies
        fputcsv($handle, ['Recent Policies']);
        fputcsv($handle, ['Policy No', 'Client', 'Start Date', 'End Date', 'Premium']);
        Policy::with('client')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get()
            ->each(function($policy) use ($handle) {
                fputcsv($handle, [
                    $policy->policy_no,
                    $policy->client_name ?? 'N/A',
                    $policy->start_date ? $policy->start_date->format('Y-m-d') : '',
                    $policy->end_date ? $policy->end_date->format('Y-m-d') : '',
                    $policy->premium ?? 0
                ]);
            });
        fputcsv($handle, []);

        // Payments
        fputcsv($handle, ['Recent Payments']);
        fputcsv($handle, ['Reference', 'Amount', 'Paid On', 'Status']);
        Payment::whereBetween('paid_on', [$startDate, $endDate])
            ->orderBy('paid_on', 'desc')
            ->limit(50)
            ->get()
            ->each(function($payment) use ($handle) {
                fputcsv($handle, [
                    $payment->payment_reference ?? 'N/A',
                    $payment->amount ?? 0,
                    $payment->paid_on ? Carbon::parse($payment->paid_on)->format('Y-m-d') : '',
                    'Paid'
                ]);
            });

        fclose($handle);
        return response()->streamDownload(function() use ($handle) {
            //
        }, $fileName, $headers);
    }

    // Logout
    public function logout(Request $request)
    {
        // Log logout activity
        if (Auth::check()) {
            AuditLog::log('logout', null, null, null, 'User logged out');
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    /**
     * Safely count life policies, handling cases where columns might not exist
     */
    private function countLifePolicies()
    {
        try {
            // Check if policy_class_id column exists
            $columns = \Schema::getColumnListing('policies');
            if (in_array('policy_class_id', $columns)) {
                return Policy::whereHas('policyClass', function($q) {
                    $q->where('name', 'LIKE', '%Life%');
                })->count();
            }
        } catch (\Exception $e) {
            // Column doesn't exist or relationship fails
        }
        
        // Fallback: return 0 or count all policies if we can't determine
        return 0;
    }
}
