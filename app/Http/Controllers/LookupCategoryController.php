<?php
// app/Http/Controllers/LookupCategoryController.php

namespace App\Http\Controllers;

use App\Models\LookupCategory;
use App\Models\LookupValue;
use Illuminate\Http\Request;

class LookupCategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = LookupCategory::with('values');
        
        // Search by name
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        // Filter by active status
        if ($request->filled('active')) {
            $query->where('active', $request->active == '1' ? 1 : 0);
        }
        
        $categories = $query->orderBy('name')->paginate(10)->withQueryString();
        
        return view('lookup-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('lookups.create-category');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:lookup_categories,name',
            'active' => 'boolean'
        ]);

        $category = LookupCategory::create([
            'name' => $request->name,
            'active' => $request->has('active') ? (bool)$request->active : true
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Category created successfully.',
                'category' => $category
            ]);
        }

        return redirect()->route('lookup-categories.index')
            ->with('success', 'Category created successfully.');
    }

    public function edit(LookupCategory $lookupCategory)
    {
        if (request()->ajax()) {
            return response()->json($lookupCategory);
        }
        return view('lookups.edit-category', compact('lookupCategory'));
    }

    public function update(Request $request, LookupCategory $lookupCategory)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:lookup_categories,name,' . $lookupCategory->id,
            'active' => 'boolean'
        ]);

        $lookupCategory->update([
            'name' => $request->name,
            'active' => $request->has('active') ? (bool)$request->active : false
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Category updated successfully.',
                'category' => $lookupCategory->fresh()
            ]);
        }

        return redirect()->route('lookup-categories.index')
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(LookupCategory $lookupCategory)
    {
        $lookupCategory->delete();

          return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully.'
            ]);
    }
}