# Reusable Table Component System

This guide explains how to use the reusable table components system that provides:
- ✅ Drag & Drop column reordering
- ✅ Mandatory fields (always visible, cannot be unchecked)
- ✅ Column selection & customization
- ✅ Print functionality
- ✅ Consistent design across all table pages
- ✅ Responsive design

## Components Created

### 1. `resources/views/partials/table-styles.blade.php`
Contains all CSS styles for tables, modals, columns, and print functionality.

### 2. `resources/views/partials/column-selection-modal.blade.php`
Reusable column selection modal with drag & drop functionality.

### 3. `resources/views/partials/table-scripts.blade.php`
JavaScript for drag & drop, column selection, and modal management.

### 4. `resources/views/partials/data-table.blade.php`
Complete table structure component (for simpler use cases).

### 5. `app/Helpers/TableConfigHelper.php`
Configuration helper for module-specific settings.

## How to Use

### Step 1: Add Module Configuration

Edit `app/Helpers/TableConfigHelper.php` and add your module configuration:

```php
'your-module' => [
    'module' => 'your-module',
    'route_prefix' => 'your-module',
    'session_key' => 'your_module_columns',
    'default_columns' => ['col1', 'col2', 'col3'],
    'mandatory_columns' => ['col1', 'col2'], // Cannot be unchecked
    'column_definitions' => [
        'col1' => 'Column 1 Label',
        'col2' => 'Column 2 Label',
        'col3' => 'Column 3 Label',
    ],
],
```

### Step 2: Update Controller

In your controller's `index` method, pass the configuration:

```php
use App\Helpers\TableConfigHelper;

public function index(Request $request)
{
    $config = TableConfigHelper::getConfig('your-module');
    $selectedColumns = TableConfigHelper::getSelectedColumns('your-module');
    
    $data = YourModel::paginate(10);
    
    return view('your-module.index', compact('data', 'config', 'selectedColumns'));
}
```

### Step 3: Create/Update View File

#### Option A: Using the Full Component (Simpler)

```blade
@extends('layouts.app')
@section('content')

@include('partials.table-styles')

@php
  $module = 'your-module';
  $renderCell = function($row, $col) {
    // Custom cell rendering logic
    switch($col) {
      case 'status':
        return '<span class="badge-status">' . $row->status . '</span>';
      case 'date':
        return $row->date ? $row->date->format('d-M-y') : '-';
      default:
        return $row->$col ?? '-';
    }
  };
  $rowClass = function($row) {
    return $row->status === 'Archived' ? 'archived-row' : '';
  };
  $editFunction = 'openEditYourModel';
@endphp

@include('partials.data-table', [
  'module' => $module,
  'data' => $data,
  'renderCell' => $renderCell,
  'rowClass' => $rowClass,
  'editFunction' => $editFunction,
  'addButton' => ['id' => 'addBtn', 'label' => 'Add'],
  'filterButtons' => [
    ['type' => 'archived', 'id' => 'archivedBtn', 'label' => 'Archived Only']
  ],
])

<!-- Your custom modals here -->

@endsection
```

#### Option B: Manual Implementation (More Control)

```blade
@extends('layouts.app')
@section('content')

@include('partials.table-styles')

@php
  $config = \App\Helpers\TableConfigHelper::getConfig('your-module');
  $selectedColumns = \App\Helpers\TableConfigHelper::getSelectedColumns('your-module');
  $columnDefinitions = $config['column_definitions'];
  $mandatoryColumns = $config['mandatory_columns'];
@endphp

<div class="dashboard">
  <div class="container-table">
    <div style="background:#fff; border:1px solid #ddd; border-radius:4px; overflow:hidden;">
      <div class="page-header">
        <div class="page-title-section">
          <h3>Your Module</h3>
          <div class="records-found">Records Found - {{ $data->total() }}</div>
        </div>
        <div class="action-buttons">
          <button class="btn btn-add" id="addBtn">Add</button>
        </div>
      </div>

      <div class="table-responsive" id="tableResponsive">
        <table>
          <thead>
            <tr>
              <th>Action</th>
              @foreach($selectedColumns as $col)
                <th data-column="{{ $col }}">{{ $columnDefinitions[$col] }}</th>
              @endforeach
            </tr>
          </thead>
          <tbody>
            @foreach($data as $row)
              <tr>
                <td class="action-cell">
                  <svg class="action-expand" onclick="openEdit({{ $row->id }})" ...>
                    <!-- SVG icon -->
                  </svg>
                </td>
                @foreach($selectedColumns as $col)
                  <td data-column="{{ $col }}">
                    {{ $row->$col ?? '-' }}
                  </td>
                @endforeach
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <div class="footer">
        <div class="footer-left">
          <a class="btn btn-export" href="{{ route('your-module.export') }}">Export</a>
          <button class="btn btn-column" onclick="openColumnModal()">Column</button>
        </div>
        <!-- Pagination -->
      </div>
    </div>
  </div>
</div>

@include('partials.column-selection-modal', [
  'selectedColumns' => $selectedColumns,
  'columnDefinitions' => $columnDefinitions,
  'mandatoryColumns' => $mandatoryColumns,
  'columnSettingsRoute' => route('your-module.save-column-settings'),
])

<script>
  const selectedColumns = @json($selectedColumns);
</script>
@include('partials.table-scripts', ['mandatoryColumns' => $mandatoryColumns])

@endsection
```

### Step 4: Add Routes (if not exists)

```php
Route::post('/your-module/save-column-settings', [YourController::class, 'saveColumnSettings'])
    ->name('your-module.save-column-settings');
Route::get('/your-module/export', [YourController::class, 'export'])
    ->name('your-module.export');
```

### Step 5: Implement Controller Methods

```php
public function saveColumnSettings(Request $request)
{
    session(['your_module_columns' => $request->columns ?? []]);
    return redirect()->route('your-module.index')
        ->with('success', 'Column settings saved successfully.');
}
```

## Features

### 1. Drag & Drop Columns
Users can drag columns in the selection modal to reorder them. The order is preserved in the table.

### 2. Mandatory Fields
Fields marked as mandatory are:
- Always checked
- Cannot be unchecked
- Always included when saving

### 3. Print Functionality
Built-in print styles that:
- Hide all UI elements
- Show only the table
- Format for A4 landscape printing

### 4. Responsive Design
- Mobile-friendly layouts
- Adaptive column layouts
- Touch-friendly controls

## Examples

See `resources/views/contacts/index.blade.php` for a complete implementation example.

## Notes

- The table respects column order from session
- Mandatory columns are enforced on both frontend and backend
- Print styles are automatically applied
- All components use consistent styling from clients page

