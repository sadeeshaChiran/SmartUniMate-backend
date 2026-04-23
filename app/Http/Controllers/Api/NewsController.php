<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    // Get news list
    public function index()
    {
        return News::with('category')->latest()->get();
    }

    // Create news
    public function store(Request $request)
    {
        $news = News::create($request->validate([
            'title' => 'required',
            'content' => 'required',
            'sub_topic' => 'nullable',
            'category_id' => 'required|exists:news_categories,id',
            'date' => 'required|date'
        ]));

        return response()->json($news, 201);
    }

    // Find news
    public function show($id)
    {
        return News::with('category')->findOrFail($id);
    }

    // Update news
    public function update(Request $request, $id)
    {
        $news = News::findOrFail($id);

        $news->update($request->validate([
            'title' => 'required',
            'content' => 'required',
            'sub_topic' => 'nullable',
            'category_id' => 'required|exists:news_categories,id',
            'date' => 'required|date'
        ]));

        return response()->json($news);
    }

    // Delete news
    public function destroy($id)
    {
        News::findOrFail($id)->delete();
        return response()->json(['message' => 'Deleted']);
    }

    // Get news by category
    public function byCategory($id)
    {
        $news = News::with('category')
            ->where('category_id', $id)
            ->latest()
            ->paginate(10);

        return response()->json([
            'status' => true,
            'message' => 'News by category',
            'data' => $news
        ]);
    }


    // Search news
    public function search(Request $request)
    {
        $query = News::with('category');

        if ($request->q) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->q . '%')
                    ->orWhere('content', 'like', '%' . $request->q . '%')
                    ->orWhere('sub_topic', 'like', '%' . $request->q . '%');
            });
        }

        $news = $query->latest()->paginate(10);

        return response()->json([
            'status' => true,
            'message' => 'Search results',
            'data' => $news
        ]);
    }
}
