<?php

namespace App\Http\Controllers;

use App\Models\Community;
use Illuminate\Http\Request;

class CommunityController extends Controller
{
    // GET all posts
    public function index()
    {
        return response()->json(Community::with('student')->latest()->get());
    }

    // CREATE post
    public function store(Request $request)
    {
        $request->validate([
            'post_content' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $payload = $request->all();
        $payload['user_id'] = $request->user()->id;

        $data = Community::create($payload);

        return response()->json([
            'message' => 'Post created successfully',
            'data' => $data
        ], 201);
    }

    // GET single post
    public function show($id)
    {
        $data = Community::find($id);

        if (!$data) {
            return response()->json(['message' => 'Not found'], 404);
        }

        return response()->json($data);
    }

    // UPDATE post
    public function update(Request $request, $id)
    {
        $data = Community::find($id);

        if (!$data) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $data->update($request->all());

        return response()->json([
            'message' => 'Updated successfully',
            'data' => $data
        ]);
    }

    // DELETE post
    public function destroy($id)
    {
        $data = Community::find($id);

        if (!$data) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $data->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }
}