<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lookup Tables Management</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
</head>
<body>
@extends('layouts.app')
@section('content')
<link rel="stylesheet" href="{{ asset('css/lookups-index.css') }}">


<div class="dashboard">
    <div class="container-table">
        <h3>Lookup Tables Management</h3>
        @if(session('success'))
        <div class="alert alert-success" id="successAlert">
            {{ session('success') }}
            <button type="button" class="alert-close" onclick="document.getElementById('successAlert').style.display='none'">×</button>
        </div>
        @endif
        <div class="top-bar">
            <div class="records-found">Total {{ $categories->count() }} Categories Found</div>
            <div>
                <input type="text" id="searchInput" class="search-box" placeholder="Search categories...">
            </div>
            <div class="action-buttons">
                <button type="button" class="btn btn-add" onclick="openAddCategoryModal()">
                    <i class="fas fa-plus"></i> Add Category
                </button>
                <button class="btn btn-back" onclick="window.history.back()">
                    <i class="fas fa-arrow-left"></i> Back
                </button>
            </div>
        </div>
        <div class="table-responsive">
            <table id="lookupTable">
                <thead>
                    <tr>
                        <th>Seq</th>
                        <th>Category Name</th>
                        <th>Active</th>
                        <th>Values Count</th>
                        <th>Description/Code</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categories as $category)
                    <tr class="category-header" data-category="{{ strtolower($category->name) }}">
                        <td>{{ $loop->iteration }}</td>
                        <td><strong>{{ $category->name }}</strong></td>
                        <td>
                            <span class="{{ $category->active ? 'status-active' : 'status-inactive' }}">
                                {{ $category->active ? 'Yes' : 'No' }}
                            </span>
                        </td>
                        <td>{{ $category->values->count() }}</td>
                        <td>-</td>
                        <td>
                            <button type="button" class="btn-action btn-add-value" 
                                    onclick="openAddValueModal({{ $category->id }}, '{{ $category->name }}')"
                                    title="Add Value">
                                <i class="fas fa-plus"></i> Add Value
                            </button>
                            <button type="button" class="btn-action btn-edit" 
                                    onclick="openEditCategoryModal({{ $category->id }}, '{{ $category->name }}', {{ $category->active ? 'true' : 'false' }})"
                                    title="Edit Category">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ route('lookup-categories.destroy', $category) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-action btn-delete" title="Delete Category" 
                                        onclick="return confirm('Are you sure you want to delete this value?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @foreach($category->values as $value)
                    <tr data-category="{{ strtolower($category->name) }}">
                        <td>{{ $value->seq }}</td>
                        <td>{{ $value->name }}</td>
                        <td>
                            <span class="{{ $value->active ? 'status-active' : 'status-inactive' }}">
                                {{ $value->active ? 'Yes' : 'No' }}
                            </span>
                        </td>
                        <td>-</td>
                        <td>
                            @if($value->description)
                                {{ $value->description }}
                            @elseif($value->code)
                                Code: {{ $value->code }}
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            <button type="button" class="btn-action btn-edit" 
                                    onclick="openEditValueModal({{ $value->id }}, {{ $value->seq }}, '{{ $value->name }}', '{{ $value->description }}', '{{ $value->code }}', {{ $value->active ? 'true' : 'false' }})"
                                    title="Edit Value">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ route('lookup-values.destroy', $value) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-action btn-delete" title="Delete Value" 
                                        onclick="return confirm('Kya aap sure hain ke aap ye value delete karna chahte hain?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                    @if($category->values->count() == 0)
                    <tr data-category="{{ strtolower($category->name) }}">
                        <td colspan="6" style="text-align: center; color: #6c757d;">
                            Is category mein koi values nahi hain
                        </td>
                    </tr>
                    @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Category Modal -->
    <div class="modal" id="addCategoryModal">
        <div class="modal-content">
            <div class="modal-header">
                <span class="modal-title">Add New Category</span>
                <button type="button" class="modal-close" onclick="closeModal('addCategoryModal')">×</button>
            </div>
            <form id="addCategoryForm" action="{{ route('lookup-categories.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="categoryName" class="form-label">Category Name</label>
                        <input type="text" class="form-control" id="categoryName" name="name" required>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="categoryActive" name="active" value="1" checked>
                        <label class="form-check-label" for="categoryActive">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-back" onclick="closeModal('addCategoryModal')">Cancel</button>
                    <button type="submit" class="btn btn-add">Save Category</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Category Modal -->
    <div class="modal" id="editCategoryModal">
        <div class="modal-content">
            <div class="modal-header">
                <span class="modal-title">Edit Category</span>
                <button type="button" class="modal-close" onclick="closeModal('editCategoryModal')">×</button>
            </div>
            <form id="editCategoryForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editCategoryName" class="form-label">Category Name</label>
                        <input type="text" class="form-control" id="editCategoryName" name="name" required>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="editCategoryActive" name="active" value="1">
                        <label class="form-check-label" for="editCategoryActive">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-back" onclick="closeModal('editCategoryModal')">Cancel</button>
                    <button type="submit" class="btn btn-add">Update Category</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Value Modal -->
    <div class="modal" id="addValueModal">
        <div class="modal-content">
            <div class="modal-header">
                <span class="modal-title">Add Value to <span id="addValueCategoryName"></span></span>
                <button type="button" class="modal-close" onclick="closeModal('addValueModal')">×</button>
            </div>
            <form id="addValueForm" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="addValueCategoryId" name="lookup_category_id">
                    <div class="mb-3">
                        <label for="valueSeq" class="form-label">Sequence Number</label>
                        <input type="number" class="form-control" id="valueSeq" name="seq" required>
                    </div>
                    <div class="mb-3">
                        <label for="valueName" class="form-label">Value Name</label>
                        <input type="text" class="form-control" id="valueName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="valueDescription" class="form-label">Description (Optional)</label>
                        <textarea class="form-control" id="valueDescription" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="valueCode" class="form-label">Code (Optional)</label>
                        <input type="text" class="form-control" id="valueCode" name="code">
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="valueActive" name="active" value="1" checked>
                        <label class="form-check-label" for="valueActive">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-back" onclick="closeModal('addValueModal')">Cancel</button>
                    <button type="submit" class="btn btn-add">Save Value</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Value Modal -->
    <div class="modal" id="editValueModal">
        <div class="modal-content">
            <div class="modal-header">
                <span class="modal-title">Edit Value</span>
                <button type="button" class="modal-close" onclick="closeModal('editValueModal')">×</button>
            </div>
            <form id="editValueForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editValueSeq" class="form-label">Sequence Number</label>
                        <input type="number" class="form-control" id="editValueSeq" name="seq" required>
                    </div>
                    <div class="mb-3">
                        <label for="editValueName" class="form-label">Value Name</label>
                        <input type="text" class="form-control" id="editValueName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="editValueDescription" class="form-label">Description (Optional)</label>
                        <textarea class="form-control" id="editValueDescription" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="editValueCode" class="form-label">Code (Optional)</label>
                        <input type="text" class="form-control" id="editValueCode" name="code">
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="editValueActive" name="active" value="1">
                        <label class="form-check-label" for="editValueActive">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-back" onclick="closeModal('editValueModal')">Cancel</button>
                    <button type="submit" class="btn btn-add">Update Value</button>
                </div>
            </form>
        </div>
    </div>
</div>


<script src="{{ asset('js/lookups-index.js') }}"></script>
@endsection
</body>
</html>
