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
            'category'  => 'required|string',
            'title'     => 'required|string',
            'source'    => 'nullable|string',
            'status'    => 'nullable|string',
            'file'      => 'nullable|file|max:20480',
            'url'       => 'nullable|string',
        ]);

        $fields = $request->only(['category', 'title', 'source', 'status', 'url']);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
            
            $destinationPath = public_path('uploads/kb');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            
            $file->move($destinationPath, $filename);
            $fields['file_path'] = '/uploads/kb/' . $filename;
        }

        $data = KnowledgeBase::create($fields);

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