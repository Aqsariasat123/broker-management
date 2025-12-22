@extends('layouts.app')
@section('content')

@include('partials.table-styles')

<div class="dashboard">
  <div class="container-table">
    <div style="background:#fff; border:1px solid #ddd; border-radius:4px; overflow:hidden; margin-bottom:15px;">
      <div class="page-header" style="background:#fff; border-bottom:1px solid #ddd; margin-bottom:0;">
        <div class="page-title-section">
          <h3>Lookup Categories</h3>
          <div class="records-found">Records Found - {{ $categories->total() }}</div>
        </div>
        <div class="action-buttons">
          <button class="btn btn-add" onclick="openCategoryDialog()">Add</button>
          <a href="{{ route('dashboard') }}" class="btn" style="background:#6c757d; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer; text-decoration:none; font-size:13px;">Back</a>
        </div>
      </div>
      
      <!-- Filters -->
      <div style="padding:15px 20px; border-bottom:1px solid #ddd; background:#f8f9fa; display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
        <div style="flex:1; min-width:200px;">
          <input type="text" id="searchInput" placeholder="Search by name..." value="{{ request('search') }}" style="width:100%; padding:6px 12px; border:1px solid #ccc; border-radius:2px; font-size:13px;">
        </div>
        <div>
          <select id="activeFilter" style="padding:6px 12px; border:1px solid #ccc; border-radius:2px; font-size:13px;">
            <option value="">All Status</option>
            <option value="1" {{ request('active') == '1' ? 'selected' : '' }}>Active</option>
            <option value="0" {{ request('active') == '0' ? 'selected' : '' }}>Inactive</option>
          </select>
        </div>
        <div>
          <button onclick="applyFilters()" class="btn" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer; font-size:13px;">Filter</button>
        </div>
        <div>
          <button onclick="clearFilters()" class="btn" style="background:#6c757d; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer; font-size:13px;">Clear</button>
        </div>
      </div>

      @if(session('success') || request()->get('success'))
        <div class="alert alert-success" id="successAlert" style="padding:8px 12px; margin:15px 20px; border:1px solid #c3e6cb; background:#d4edda; color:#155724;">
          {{ session('success') ?? request()->get('success') }}
          <button type="button" class="alert-close" onclick="document.getElementById('successAlert').style.display='none'" style="float:right;background:none;border:none;font-size:16px;cursor:pointer;">×</button>
        </div>
      @endif

      <div class="table-responsive" id="tableResponsive">
        <table id="categoriesTable">
          <thead>
            <tr>
              <th>Action</th>
              <th>ID</th>
              <th>Category Name</th>
              <th>Active</th>
              <th>Values Count</th>
              <th>Created At</th>
              <th>Updated At</th>
            </tr>
          </thead>
          <tbody>
            @forelse($categories as $category)
              <tr>
                <td class="action-cell">
                  <svg class="action-expand" onclick="editCategory({{ $category->id }})" width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="cursor:pointer; vertical-align:middle;">
                    <!-- Maximize icon: four arrows pointing outward from center -->
                    <!-- Top arrow -->
                    <path d="M12 2L12 8M12 2L10 4M12 2L14 4" stroke="#2d2d2d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <!-- Right arrow -->
                    <path d="M22 12L16 12M22 12L20 10M22 12L20 14" stroke="#2d2d2d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <!-- Bottom arrow -->
                    <path d="M12 22L12 16M12 22L10 20M12 22L14 20" stroke="#2d2d2d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <!-- Left arrow -->
                    <path d="M2 12L8 12M2 12L4 10M2 12L4 14" stroke="#2d2d2d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                </td>
                <td>{{ $category->id }}</td>
                <td><strong>{{ $category->name }}</strong></td>
                <td>
                  <span style="color: {{ $category->active ? '#28a745' : '#dc3545' }}; font-weight:bold;">
                    {{ $category->active ? 'Yes' : 'No' }}
                  </span>
                </td>
                <td>{{ $category->values->count() }}</td>
                <td>{{ $category->created_at ? $category->created_at->format('d-M-y') : '—' }}</td>
                <td>{{ $category->updated_at ? $category->updated_at->format('d-M-y') : '—' }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="7" style="text-align:center; padding:20px; color:#6c757d;">No categories found</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      @if($categories->hasPages())
        <div class="paginator" style="padding:15px 20px; border-top:1px solid #ddd; display:flex; align-items:center; justify-content:center; gap:8px;">
          @php
            $base = url()->current();
            $q = request()->query();
            $current = $categories->currentPage();
            $last = max(1, $categories->lastPage());
            function page_url($base, $q, $p) {
              $params = array_merge($q, ['page' => $p]);
              return $base . '?' . http_build_query($params);
            }
          @endphp

          <a class="btn-page" href="{{ $current > 1 ? page_url($base, $q, 1) : '#' }}" @if($current <= 1) style="opacity:0.5; cursor:not-allowed;" @endif>&laquo;</a>
          <a class="btn-page" href="{{ $current > 1 ? page_url($base, $q, $current - 1) : '#' }}" @if($current <= 1) style="opacity:0.5; cursor:not-allowed;" @endif>&lsaquo;</a>

          <span style="padding:0 8px;">Page {{ $current }} of {{ $last }}</span>

          <a class="btn-page" href="{{ $current < $last ? page_url($base, $q, $current + 1) : '#' }}" @if($current >= $last) style="opacity:0.5; cursor:not-allowed;" @endif>&rsaquo;</a>
          <a class="btn-page" href="{{ $current < $last ? page_url($base, $q, $last) : '#' }}" @if($current >= $last) style="opacity:0.5; cursor:not-allowed;" @endif>&raquo;</a>
        </div>
      @endif
    </div>
  </div>
</div>

<!-- Category Modal Dialog -->
<div id="categoryModal" class="modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center;">
  <div class="modal-content" style="background:#fff; border-radius:6px; width:95%; max-width:500px; box-shadow:0 4px 6px rgba(0,0,0,0.1); padding:0;">
    <div class="modal-header" style="background-color:#f8f9fa; border-bottom:1px solid #dee2e6; padding:15px 20px; display:flex; align-items:center; justify-content:space-between;">
      <h3 id="categoryModalTitle" style="margin:0; font-size:18px; font-weight:600;">Add Category</h3>
      <div style="display:flex; gap:8px;">
        <button type="button" class="btn-save" onclick="saveCategory()" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer; font-size:13px;">Save</button>
        <button type="button" class="modal-close" onclick="closeCategoryDialog()" style="background:none; border:none; font-size:24px; cursor:pointer; color:#666; padding:0; width:30px; height:30px; display:flex; align-items:center; justify-content:center;">×</button>
      </div>
    </div>
    <form id="categoryForm" style="margin:0;">
      @csrf
      <input type="hidden" id="category_id" name="id">
      <div class="modal-body" style="padding:20px;">
        <div class="form-group" style="margin-bottom:15px;">
          <label style="display:block; margin-bottom:5px; font-weight:600; font-size:13px;">Category Name <span style="color:red;">*</span></label>
          <input type="text" name="name" id="category_name" class="form-control" required style="width:100%; padding:8px; font-size:13px; border:1px solid #ccc; border-radius:2px;">
        </div>
        <div class="form-group" style="margin-bottom:15px;">
          <label style="display:flex; align-items:center; gap:8px; font-weight:600; font-size:13px; cursor:pointer;">
            <input type="checkbox" name="active" id="category_active" value="1" checked style="width:18px; height:18px; cursor:pointer;">
            <span>Active</span>
          </label>
        </div>
      </div>
    </form>
  </div>
</div>



<script>
  // Initialize data from Blade
  const lookupCategoriesIndexRoute = '{{ route("lookup-categories.index") }}';
</script>
<script src="{{ asset('js/lookup-categories-index.js') }}"></script>
@endsection

