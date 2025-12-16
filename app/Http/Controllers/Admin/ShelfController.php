<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shelf;
use Illuminate\Http\Request;

class ShelfController extends Controller
{
    public function index()
    {
        $shelves = Shelf::orderBy('code')->paginate(15);
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
        ]);
        
        Shelf::create($request->all());
        
        return redirect()->route('admin.shelves.index')
            ->with('success', 'Rak berhasil ditambahkan.');
    }

    public function edit(Shelf $shelf)
    {
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
        ]);
        
        $shelf->update($request->all());
        
        return redirect()->route('admin.shelves.index')
            ->with('success', 'Rak berhasil diperbarui.');
    }

    public function destroy(Shelf $shelf)
    {
        $shelf->delete();
        
        return redirect()->route('admin.shelves.index')
            ->with('success', 'Rak berhasil dihapus.');
    }
}
