<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shelf;
use App\Models\ShelfColumn;
use Illuminate\Http\Request;

class ShelfController extends Controller
{
    public function index()
    {
        $shelves = Shelf::withCount(['books', 'columns'])->orderBy('code')->paginate(15);
        return view('admin.shelves.index', compact('shelves'));
    }

    public function create()
    {
        return view('admin.shelves.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:20|unique:shelves,code',
            'name' => 'required|string|max:100',
            'location' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'capacity' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
            'columns' => 'nullable|array',
            'columns.*' => 'required|string|max:50',
        ]);
        
        $shelf = Shelf::create($request->except('columns'));
        
        // Create columns if provided
        if ($request->has('columns')) {
            foreach ($request->columns as $columnName) {
                if (trim($columnName)) {
                    $shelf->columns()->create(['name' => trim($columnName)]);
                }
            }
        }
        
        return redirect()->route('admin.shelves.index')
            ->with('success', 'Rak berhasil ditambahkan.');
    }

    public function edit(Shelf $shelf)
    {
        $shelf->load('columns');
        return view('admin.shelves.edit', compact('shelf'));
    }

    public function update(Request $request, Shelf $shelf)
    {
        $request->validate([
            'code' => 'required|string|max:20|unique:shelves,code,' . $shelf->id,
            'name' => 'required|string|max:100',
            'location' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'capacity' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
            'columns' => 'nullable|array',
            'columns.*' => 'required|string|max:50',
        ]);
        
        $shelf->update($request->except('columns'));
        
        // Sync columns - delete old ones and create new ones
        $existingColumns = $shelf->columns()->pluck('name')->toArray();
        $newColumns = array_filter(array_map('trim', $request->columns ?? []));
        
        // Delete removed columns
        $shelf->columns()->whereNotIn('name', $newColumns)->delete();
        
        // Add new columns
        foreach ($newColumns as $columnName) {
            if (!in_array($columnName, $existingColumns)) {
                $shelf->columns()->create(['name' => $columnName]);
            }
        }
        
        return redirect()->route('admin.shelves.index')
            ->with('success', 'Rak berhasil diperbarui.');
    }

    public function destroy(Shelf $shelf)
    {
        $shelf->delete();
        
        return redirect()->route('admin.shelves.index')
            ->with('success', 'Rak berhasil dihapus.');
    }

    /**
     * Get columns for a shelf (AJAX)
     */
    public function getColumns(Shelf $shelf)
    {
        return response()->json(
            $shelf->columns()->active()->orderBy('name')->get(['id', 'name'])
        );
    }
}

