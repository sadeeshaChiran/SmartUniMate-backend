<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NewsCategory;
use Illuminate\Http\Request;

class NewsCategoryController extends Controller
{
    // Get news category list
    public function index()
    {
        return response()->json(NewsCategory::all());
    }

    // Create news category
    public function store(Request $request)
    {
        $category = NewsCategory::create($request->validate([
            'name' => 'required|string|max:255'
        ]));

        return response()->json($category, 201);
    }

    // Find news category
    public function show($id)
    {
        return NewsCategory::findOrFail($id);
    }

    // Update news category
    public function update(Request $request, $id)
    {
        $category = NewsCategory::findOrFail($id);

        $category->update($request->validate([
            'name' => 'required|string|max:255'
        ]));

        return response()->json($category);
    }

    // Delete news category
    public function destroy($id)
    {
        NewsCategory::findOrFail($id)->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
