<?php
// app/Http/Controllers/LookupValueController.php

namespace App\Http\Controllers;

use App\Models\LookupCategory;
use App\Models\LookupValue;
use Illuminate\Http\Request;

class LookupValueController extends Controller
{
    public function index(Request $request)
    {
        $categoryId = $request->get('category_id');
        $valuesQuery = LookupValue::with('category')->orderBy('lookup_category_id')->orderBy('seq');

        // Filter by category
        if ($categoryId) {
            $valuesQuery->where('lookup_category_id', $categoryId);
        }
        
        // Search by name
        if ($request->filled('search')) {
            $valuesQuery->where('name', 'like', '%' . $request->search . '%');
        }
        
        // Filter by active status
        if ($request->filled('active')) {
            $valuesQuery->where('active', $request->active == '1' ? 1 : 0);
        }
        
        // Search by code
        if ($request->filled('code')) {
            $valuesQuery->where('code', 'like', '%' . $request->code . '%');
        }
        
        // Search by type
        if ($request->filled('type')) {
            $valuesQuery->where('type', 'like', '%' . $request->type . '%');
        }

        $values = $valuesQuery->paginate(10)->withQueryString();
        $categories = \App\Models\LookupCategory::orderBy('name')->get();

        return view('lookup-values.index', compact('values', 'categories', 'categoryId'));
    }

    public function create(LookupCategory $lookupCategory)
    {
        return view('lookups.create-value', compact('lookupCategory'));
    }

    public function store(Request $request, LookupCategory $lookupCategory = null)
    {
        // If lookupCategory is provided from route, use it; otherwise use from request
        $categoryId = $lookupCategory ? $lookupCategory->id : $request->lookup_category_id;
        
        $request->validate([
            'lookup_category_id' => $lookupCategory ? 'nullable' : 'required|exists:lookup_categories,id',
            'seq' => 'required|integer',
            'name' => 'required|string|max:255',
            'active' => 'boolean',
            'description' => 'nullable|string',
            'type' => 'nullable|string',
            'code' => 'nullable|string'
        ]);

        $finalCategoryId = $categoryId ?: $request->lookup_category_id;

        // Check for unique seq within category
        $exists = LookupValue::where('lookup_category_id', $finalCategoryId)
            ->where('seq', $request->seq)
            ->exists();

        if ($exists) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sequence number already exists for this category.'
                ], 422);
            }
            return back()->withErrors(['seq' => 'Sequence number already exists for this category.'])->withInput();
        }

        $value = LookupValue::create([
            'lookup_category_id' => $finalCategoryId,
            'seq' => $request->seq,
            'name' => $request->name,
            'active' => $request->has('active') ? (bool)$request->active : true,
            'description' => $request->description,
            'type' => $request->type,
            'code' => $request->code
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Lookup value created successfully.',
                'value' => $value->load('category')
            ]);
        }

        // Redirect based on which route was used
        if ($lookupCategory) {
            return redirect()->route('lookups.index')
                ->with('success', 'Lookup value created successfully.');
        }

        return redirect()->route('lookup-values.index')
            ->with('success', 'Lookup value created successfully.');
    }

    public function edit(LookupValue $lookupValue)
    {
        if (request()->ajax()) {
            return response()->json($lookupValue->load('category'));
        }
        return view('lookups.edit-value', compact('lookupValue'));
    }

    public function update(Request $request, LookupValue $lookupValue)
    {
        $request->validate([
            'lookup_category_id' => 'required|exists:lookup_categories,id',
            'seq' => 'required|integer',
            'name' => 'required|string|max:255',
            'active' => 'boolean',
            'description' => 'nullable|string',
            'type' => 'nullable|string',
            'code' => 'nullable|string'
        ]);

        // Check for unique seq within category (excluding current record)
        $exists = LookupValue::where('lookup_category_id', $request->lookup_category_id)
            ->where('seq', $request->seq)
            ->where('id', '!=', $lookupValue->id)
            ->exists();

        if ($exists) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sequence number already exists for this category.'
                ], 422);
            }
            return back()->withErrors(['seq' => 'Sequence number already exists for this category.'])->withInput();
        }

        $lookupValue->update([
            'lookup_category_id' => $request->lookup_category_id,
            'seq' => $request->seq,
            'name' => $request->name,
            'active' => $request->has('active') ? (bool)$request->active : false,
            'description' => $request->description,
            'type' => $request->type,
            'code' => $request->code
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Lookup value updated successfully.',
                'value' => $lookupValue->fresh()->load('category')
            ]);
        }

        return redirect()->route('lookup-values.index')
            ->with('success', 'Lookup value updated successfully.');
    }

    public function destroy(LookupValue $lookupValue)
    {
        $lookupValue->delete();

          return response()->json([
        'success' => true,
        'message' => 'Lookup value deleted successfully.'
    ]);
    }
}