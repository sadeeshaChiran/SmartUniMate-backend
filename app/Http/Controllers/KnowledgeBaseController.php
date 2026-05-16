<?php

namespace App\Http\Controllers;

use App\Models\KnowledgeBase;
use Illuminate\Http\Request;

class KnowledgeBaseController extends Controller
{
    // GET all records
    public function index()
    {
        return response()->json(KnowledgeBase::latest()->get());
    }

    // CREATE new record
    public function store(Request $request)
    {
        $request->validate([
            'category' => 'required|string',
            'title' => 'required|string',
            'source' => 'nullable|string',
            'status' => 'nullable|string',
            'file_path' => 'nullable|string',
            'url' => 'nullable|url',
        ]);

        $data = KnowledgeBase::create($request->all());

        return response()->json([
            'message' => 'Created successfully',
            'data' => $data
        ], 201);
    }

    // GET single record
    public function show($id)
    {
        $data = KnowledgeBase::find($id);

        if (!$data) {
            return response()->json(['message' => 'Not found'], 404);
        }

        return response()->json($data);
    }

    // UPDATE record
    public function update(Request $request, $id)
    {
        $data = KnowledgeBase::find($id);

        if (!$data) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $data->update($request->all());

        return response()->json([
            'message' => 'Updated successfully',
            'data' => $data
        ]);
    }

    // DELETE record
    public function destroy($id)
    {
        $data = KnowledgeBase::find($id);

        if (!$data) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $data->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }
}