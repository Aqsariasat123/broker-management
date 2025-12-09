<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\LookupCategoryController;
use App\Http\Controllers\LookupValueController;
use App\Http\Controllers\PolicyController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\LifeProposalController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\ClaimController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\CommissionController;
use App\Http\Controllers\StatementController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\DebitNoteController;
use App\Http\Controllers\PaymentPlanController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\CalendarController;




Route::get('/', function () {
    return redirect('/login');
});

// Login routes
Route::get('/login', function () {
    // If user is logged in, redirect to dashboard
    if (auth()->check()) {
        return redirect('/dashboard');
    }
    return app(AuthController::class)->showLoginForm();
})->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// All protected routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard/export', [AuthController::class, 'exportDashboard'])->name('dashboard.export');

    // User Management
    Route::prefix('users')->name('users.')->group(function () {
        // All users can view index
        Route::get('/', [UserController::class, 'index'])->name('index');
        
        // Admin only routes - must come before parameterized routes
        Route::middleware('role:admin')->group(function () {
            Route::get('/create', [UserController::class, 'create'])->name('create');
            Route::post('/', [UserController::class, 'store'])->name('store');
        });
        
        // All users can view show
        Route::get('/{user}', [UserController::class, 'show'])->name('show');
        
        // Admin only routes for editing/deleting
        Route::middleware('role:admin')->group(function () {
            Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
            Route::put('/{user}', [UserController::class, 'update'])->name('update');
            Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        });
    });

    // Permissions Management (Admin only)
    Route::middleware('role:admin')->prefix('permissions')->name('permissions.')->group(function () {
        Route::get('/', [PermissionController::class, 'index'])->name('index');
        Route::get('/create', [PermissionController::class, 'create'])->name('create');
        Route::post('/', [PermissionController::class, 'store'])->name('store');
        Route::get('/{permission}/edit', [PermissionController::class, 'edit'])->name('edit');
        Route::put('/{permission}', [PermissionController::class, 'update'])->name('update');
        Route::delete('/{permission}', [PermissionController::class, 'destroy'])->name('destroy');
    });

    // Audit Logs (Admin only)
    Route::middleware('role:admin')->prefix('audit-logs')->name('audit-logs.')->group(function () {
        Route::get('/', [AuditLogController::class, 'index'])->name('index');
        Route::get('/{auditLog}', [AuditLogController::class, 'show'])->name('show');
    });

    // Roles Management (Admin only)
    Route::middleware('role:admin')->prefix('roles')->name('roles.')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('index');
        Route::get('/create', [RoleController::class, 'create'])->name('create');
        Route::post('/', [RoleController::class, 'store'])->name('store');
        Route::get('/{role}/edit', [RoleController::class, 'edit'])->name('edit');
        Route::put('/{role}', [RoleController::class, 'update'])->name('update');
        Route::delete('/{role}', [RoleController::class, 'destroy'])->name('destroy');
        Route::put('/{role}/permissions', [RoleController::class, 'updatePermissions'])->name('permissions.update');
    });

    Route::prefix('tasks')->group(function () {
        Route::get('/', [TaskController::class, 'index'])->name('tasks.index');
        Route::post('/', [TaskController::class, 'store'])->name('tasks.store');
        Route::get('/export', [TaskController::class, 'export'])->name('tasks.export');
        Route::post('/columns/settings', [TaskController::class, 'saveColumnSettings'])->name('tasks.save-column-settings');
        Route::get('/{task}/get', [TaskController::class, 'getTask'])->name('tasks.get');
        Route::get('/{task}/edit', [TaskController::class, 'edit'])->name('tasks.edit');
        Route::get('/{task}', [TaskController::class, 'show'])->name('tasks.show');
        Route::put('/{task}', [TaskController::class, 'update'])->name('tasks.update');
        Route::delete('/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
    });

    Route::prefix('lookups')->group(function () {
        Route::get('/', [LookupCategoryController::class, 'index'])->name('lookups.index');
        Route::get('/categories/create', [LookupCategoryController::class, 'create'])->name('lookup-categories.create');
        Route::post('/categories', [LookupCategoryController::class, 'store'])->name('lookup-categories.store');
        Route::get('/categories/{lookupCategory}/edit', [LookupCategoryController::class, 'edit'])->name('lookup-categories.edit');
        Route::put('/categories/{lookupCategory}', [LookupCategoryController::class, 'update'])->name('lookup-categories.update');
        Route::delete('/categories/{lookupCategory}', [LookupCategoryController::class, 'destroy'])->name('lookup-categories.destroy');
        Route::get('/categories/{lookupCategory}/values/create', [LookupValueController::class, 'create'])->name('lookup-values.create');
        Route::post('/categories/{lookupCategory}/values', [LookupValueController::class, 'store'])->name('lookup-values.store');
        Route::get('/values/{lookupValue}/edit', [LookupValueController::class, 'edit'])->name('lookup-values.edit');
        Route::put('/values/{lookupValue}', [LookupValueController::class, 'update'])->name('lookup-values.update');
        Route::delete('/values/{lookupValue}', [LookupValueController::class, 'destroy'])->name('lookup-values.destroy');
    });

    // Policies Routes
    Route::get('/policies', [PolicyController::class, 'index'])->name('policies.index');
    Route::get('/policies/create', [PolicyController::class, 'create'])->name('policies.create');
    Route::post('/policies', [PolicyController::class, 'store'])->name('policies.store');
    Route::get('/policies/{policy}', [PolicyController::class, 'show'])->name('policies.show');
    Route::get('/policies/{policy}/edit', [PolicyController::class, 'edit'])->name('policies.edit');
    Route::put('/policies/{policy}', [PolicyController::class, 'update'])->name('policies.update');
    Route::delete('/policies/{policy}', [PolicyController::class, 'destroy'])->name('policies.destroy');
    Route::get('/policies/export', [PolicyController::class, 'export'])->name('policies.export');
    Route::post('/policies/save-column-settings', [PolicyController::class, 'saveColumnSettings'])->name('policies.save-column-settings');

    // Clients Routes
    Route::get('/clients/export', [ClientController::class, 'export'])->name('clients.export');
    Route::get('/clients/create', [ClientController::class, 'create'])->name('clients.create');
    Route::resource('clients', ClientController::class)->only(['index', 'store', 'update', 'destroy', 'show']);
    Route::get('/clients/{client}/edit', [ClientController::class, 'edit'])->name('clients.edit');
    Route::post('/clients/save-column-settings', [ClientController::class, 'saveColumnSettings'])->name('clients.save-column-settings');
    Route::post('/clients/{client}/upload-photo', [ClientController::class, 'uploadPhoto'])->name('clients.upload-photo');
    Route::post('/clients/{client}/upload-document', [ClientController::class, 'uploadDocument'])->name('clients.upload-document');

    // Contacts Routes
    Route::get('/contacts/export', [ContactController::class, 'export'])->name('contacts.export');
    Route::resource('contacts', ContactController::class)->only(['index', 'store', 'update', 'destroy', 'show']);
    Route::get('/contacts/{contact}/edit', [ContactController::class, 'edit'])->name('contacts.edit');
    Route::post('/contacts/save-column-settings', [ContactController::class, 'saveColumnSettings'])->name('contacts.save-column-settings');

    // Life Proposals Routes
    Route::get('/life-proposals/export', [LifeProposalController::class, 'export'])->name('life-proposals.export');
    Route::post('/life-proposals/save-column-settings', [LifeProposalController::class, 'saveColumnSettings'])->name('life-proposals.save-column-settings');
    Route::get('/life-proposals/{lifeProposal}/edit', [LifeProposalController::class, 'edit'])->name('life-proposals.edit');
    Route::resource('life-proposals', LifeProposalController::class)->only(['index', 'store', 'update', 'destroy', 'show']);

    // Expenses Routes
    Route::get('/expenses/export', [ExpenseController::class, 'export'])->name('expenses.export');
    Route::post('/expenses/save-column-settings', [ExpenseController::class, 'saveColumnSettings'])->name('expenses.save-column-settings');
    Route::get('/expenses/{expense}/edit', [ExpenseController::class, 'edit'])->name('expenses.edit');
    Route::get('/expenses/{expense}', [ExpenseController::class, 'show'])->name('expenses.show');
    Route::get('/expenses', [ExpenseController::class, 'index'])->name('expenses.index');
    Route::post('/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
    Route::put('/expenses/{expense}', [ExpenseController::class, 'update'])->name('expenses.update');
    Route::delete('/expenses/{expense}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');

    // Documents Routes
    Route::get('/documents/export', [DocumentController::class, 'export'])->name('documents.export');
    Route::post('/documents/save-column-settings', [DocumentController::class, 'saveColumnSettings'])->name('documents.save-column-settings');
    Route::get('/documents/{document}/edit', [DocumentController::class, 'edit'])->name('documents.edit');
    Route::get('/documents/{document}', [DocumentController::class, 'show'])->name('documents.show');
    Route::get('/documents', [DocumentController::class, 'index'])->name('documents.index');
    Route::post('/documents', [DocumentController::class, 'store'])->name('documents.store');
    Route::put('/documents/{document}', [DocumentController::class, 'update'])->name('documents.update');
    Route::delete('/documents/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy');

    // Vehicles Routes
    Route::get('/vehicles/export', [VehicleController::class, 'export'])->name('vehicles.export');
    Route::post('/vehicles/save-column-settings', [VehicleController::class, 'saveColumnSettings'])->name('vehicles.save-column-settings');
    Route::get('/vehicles/{vehicle}/edit', [VehicleController::class, 'edit'])->name('vehicles.edit');
    Route::get('/vehicles/{vehicle}', [VehicleController::class, 'show'])->name('vehicles.show');
    Route::get('/vehicles', [VehicleController::class, 'index'])->name('vehicles.index');
    Route::post('/vehicles', [VehicleController::class, 'store'])->name('vehicles.store');
    Route::put('/vehicles/{vehicle}', [VehicleController::class, 'update'])->name('vehicles.update');
    Route::delete('/vehicles/{vehicle}', [VehicleController::class, 'destroy'])->name('vehicles.destroy');

    // Claims Routes
    Route::get('/claims/export', [ClaimController::class, 'export'])->name('claims.export');
    Route::post('/claims/save-column-settings', [ClaimController::class, 'saveColumnSettings'])->name('claims.save-column-settings');
    Route::get('/claims/{claim}/edit', [ClaimController::class, 'edit'])->name('claims.edit');
    Route::get('/claims/{claim}', [ClaimController::class, 'show'])->name('claims.show');
    Route::get('/claims', [ClaimController::class, 'index'])->name('claims.index');
    Route::post('/claims', [ClaimController::class, 'store'])->name('claims.store');
    Route::put('/claims/{claim}', [ClaimController::class, 'update'])->name('claims.update');
    Route::delete('/claims/{claim}', [ClaimController::class, 'destroy'])->name('claims.destroy');

    // Income Routes
    Route::get('/incomes/export', [IncomeController::class, 'export'])->name('incomes.export');
    Route::post('/incomes/save-column-settings', [IncomeController::class, 'saveColumnSettings'])->name('incomes.save-column-settings');
    Route::get('/incomes/{income}/edit', [IncomeController::class, 'edit'])->name('incomes.edit');
    Route::get('/incomes/{income}', [IncomeController::class, 'show'])->name('incomes.show');
    Route::get('/incomes', [IncomeController::class, 'index'])->name('incomes.index');
    Route::post('/incomes', [IncomeController::class, 'store'])->name('incomes.store');
    Route::put('/incomes/{income}', [IncomeController::class, 'update'])->name('incomes.update');
    Route::delete('/incomes/{income}', [IncomeController::class, 'destroy'])->name('incomes.destroy');

    // Commissions Routes
    Route::get('/commissions/export', [CommissionController::class, 'export'])->name('commissions.export');
    Route::post('/commissions/save-column-settings', [CommissionController::class, 'saveColumnSettings'])->name('commissions.save-column-settings');
    Route::get('/commissions/{commission}/edit', [CommissionController::class, 'edit'])->name('commissions.edit');
    Route::get('/commissions/{commission}', [CommissionController::class, 'show'])->name('commissions.show');
    Route::get('/commissions', [CommissionController::class, 'index'])->name('commissions.index');
    Route::post('/commissions', [CommissionController::class, 'store'])->name('commissions.store');
    Route::put('/commissions/{commission}', [CommissionController::class, 'update'])->name('commissions.update');
    Route::delete('/commissions/{commission}', [CommissionController::class, 'destroy'])->name('commissions.destroy');

    // Statements Routes
    Route::get('/statements/export', [StatementController::class, 'export'])->name('statements.export');
    Route::post('/statements/save-column-settings', [StatementController::class, 'saveColumnSettings'])->name('statements.save-column-settings');
    Route::get('/statements/{statement}/edit', [StatementController::class, 'edit'])->name('statements.edit');
    Route::get('/statements/{statement}', [StatementController::class, 'show'])->name('statements.show');
    Route::get('/statements', [StatementController::class, 'index'])->name('statements.index');
    Route::post('/statements', [StatementController::class, 'store'])->name('statements.store');
    Route::put('/statements/{statement}', [StatementController::class, 'update'])->name('statements.update');
    Route::delete('/statements/{statement}', [StatementController::class, 'destroy'])->name('statements.destroy');

    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar');
    Route::get('/calendar/events', [CalendarController::class, 'getEvents'])->name('calendar.events');

    // Secure file download route for encrypted files
    Route::get('/secure-file/{type}/{id}', function ($type, $id) {
        try {
            if ($type === 'debit-note') {
                $debitNote = \App\Models\DebitNote::findOrFail($id);
                if (!$debitNote->document_path) {
                    abort(404, 'File not found');
                }
                
                if ($debitNote->is_encrypted ?? false) {
                    $decrypted = \App\Services\EncryptionService::getDecryptedFile($debitNote->document_path, 'encrypted');
                    $metadata = \App\Services\EncryptionService::getFileMetadata($debitNote->document_path, 'encrypted');
                    
                    // Use metadata for filename and MIME type if available
                    $filename = $metadata['original_name'] ?? 'debit_note_' . $debitNote->debit_note_no . '.pdf';
                    $mimeType = $metadata['mime_type'] ?? 'application/pdf';
                    
                    // Fallback to detection if metadata not available
                    if (!isset($metadata['mime_type']) && function_exists('finfo_open')) {
                        $finfo = finfo_open(FILEINFO_MIME_TYPE);
                        $mimeType = finfo_buffer($finfo, $decrypted) ?: $mimeType;
                        finfo_close($finfo);
                    }
                    
                    return response($decrypted)
                        ->header('Content-Type', $mimeType)
                        ->header('Content-Disposition', 'inline; filename="' . $filename . '"');
                } else {
                    // Legacy unencrypted file
                    return response()->file(storage_path('app/public/' . $debitNote->document_path));
                }
            } elseif ($type === 'payment') {
                $payment = \App\Models\Payment::findOrFail($id);
                if (!$payment->receipt_path) {
                    abort(404, 'File not found');
                }
                
                if ($payment->is_encrypted ?? false) {
                    $decrypted = \App\Services\EncryptionService::getDecryptedFile($payment->receipt_path, 'encrypted');
                    $metadata = \App\Services\EncryptionService::getFileMetadata($payment->receipt_path, 'encrypted');
                    
                    // Use metadata for filename and MIME type if available
                    $filename = $metadata['original_name'] ?? 'receipt_' . $payment->payment_reference . '.pdf';
                    $mimeType = $metadata['mime_type'] ?? 'application/pdf';
                    
                    // Fallback to detection if metadata not available
                    if (!isset($metadata['mime_type']) && function_exists('finfo_open')) {
                        $finfo = finfo_open(FILEINFO_MIME_TYPE);
                        $mimeType = finfo_buffer($finfo, $decrypted) ?: $mimeType;
                        finfo_close($finfo);
                    }
                    
                    return response($decrypted)
                        ->header('Content-Type', $mimeType)
                        ->header('Content-Disposition', 'inline; filename="' . $filename . '"');
                } else {
                    // Legacy unencrypted file
                    return response()->file(storage_path('app/public/' . $payment->receipt_path));
                }
            }
            
            abort(404, 'Invalid file type');
        } catch (\Exception $e) {
            abort(500, 'Error retrieving file: ' . $e->getMessage());
        }
    })->middleware('auth')->name('secure.file');

    // File serving route for storage files
    Route::get('/storage/{path}', function ($path) {
        $filePath = storage_path('app/public/' . $path);
        
        if (!file_exists($filePath) || !is_file($filePath)) {
            abort(404, 'File not found');
        }
        
        // Security check: ensure the file is within the public storage directory
        $realPath = realpath($filePath);
        $storagePath = realpath(storage_path('app/public'));
        
        if (!$realPath || !$storagePath) {
            abort(403, 'Access denied');
        }
        
        // Normalize paths for cross-platform compatibility (Windows case-insensitive)
        $normalizedRealPath = str_replace('\\', '/', strtolower($realPath));
        $normalizedStoragePath = str_replace('\\', '/', strtolower($storagePath));
        
        // Check if the file path starts with the storage path
        if (strpos($normalizedRealPath, $normalizedStoragePath) !== 0) {
            abort(403, 'Access denied');
        }
        
        return response()->file($filePath);
    })->where('path', '.*')->name('storage.serve');

    // Schedules Routes
    Route::prefix('schedules')->name('schedules.')->group(function () {
        Route::get('/', [ScheduleController::class, 'index'])->name('index');
        Route::get('/create', [ScheduleController::class, 'create'])->name('create');
        Route::post('/', [ScheduleController::class, 'store'])->name('store');
        Route::get('/{schedule}', [ScheduleController::class, 'show'])->name('show');
        Route::get('/{schedule}/edit', [ScheduleController::class, 'edit'])->name('edit');
        Route::put('/{schedule}', [ScheduleController::class, 'update'])->name('update');
        Route::delete('/{schedule}', [ScheduleController::class, 'destroy'])->name('destroy');
    });

    // Payment Tracking Routes
    Route::prefix('debit-notes')->name('debit-notes.')->group(function () {
        Route::get('/', [DebitNoteController::class, 'index'])->name('index');
        Route::get('/create', [DebitNoteController::class, 'create'])->name('create');
        Route::post('/', [DebitNoteController::class, 'store'])->name('store');
        Route::post('/save-column-settings', [DebitNoteController::class, 'saveColumnSettings'])->name('save-column-settings');
        Route::get('/{debitNote}/edit', [DebitNoteController::class, 'edit'])->name('edit');
        Route::get('/{debitNote}', [DebitNoteController::class, 'show'])->name('show');
        Route::put('/{debitNote}', [DebitNoteController::class, 'update'])->name('update');
        Route::delete('/{debitNote}', [DebitNoteController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('payment-plans')->name('payment-plans.')->group(function () {
        Route::get('/', [PaymentPlanController::class, 'index'])->name('index');
        Route::get('/create', [PaymentPlanController::class, 'create'])->name('create');
        Route::post('/', [PaymentPlanController::class, 'store'])->name('store');
        Route::post('/create-instalments', [PaymentPlanController::class, 'createInstalments'])->name('create-instalments');
        Route::post('/save-column-settings', [PaymentPlanController::class, 'saveColumnSettings'])->name('save-column-settings');
        Route::get('/{paymentPlan}/edit', [PaymentPlanController::class, 'edit'])->name('edit');
        Route::get('/{paymentPlan}', [PaymentPlanController::class, 'show'])->name('show');
        Route::put('/{paymentPlan}', [PaymentPlanController::class, 'update'])->name('update');
        Route::delete('/{paymentPlan}', [PaymentPlanController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/', [PaymentController::class, 'index'])->name('index');
        Route::get('/report', [PaymentController::class, 'report'])->name('report');
        Route::get('/create', [PaymentController::class, 'create'])->name('create');
        Route::post('/', [PaymentController::class, 'store'])->name('store');
        Route::post('/save-column-settings', [PaymentController::class, 'saveColumnSettings'])->name('save-column-settings');
        Route::get('/{payment}/edit', [PaymentController::class, 'edit'])->name('edit');
        Route::get('/{payment}', [PaymentController::class, 'show'])->name('show');
        Route::put('/{payment}', [PaymentController::class, 'update'])->name('update');
        Route::delete('/{payment}', [PaymentController::class, 'destroy'])->name('destroy');
    });
});

