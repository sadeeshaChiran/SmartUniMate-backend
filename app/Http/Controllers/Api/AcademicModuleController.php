<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AcademicModule;
use Illuminate\Http\Request;

class AcademicModuleController extends Controller
{
    // GET all records
    public function index()
    {
        return response()->json(AcademicModule::orderBy('code')->get());
    }

    // CREATE a new record
    public function store(Request $request)
    {
        $fields = $request->validate([
            'code'     => 'required|string|unique:academic_modules,code',
            'name'     => 'required|string',
            'credits'  => 'required|integer|min:1',
            'faculty'  => 'nullable|string',
            'desc'     => 'nullable|string',
            'prereq'   => 'nullable|string',
        ]);

        $data = AcademicModule::create($fields);

        return response()->json([
            'message' => 'Academic module created successfully',
            'data'    => $data
        ], 201);
    }

    // GET single record
    public function show($id)
    {
        $data = AcademicModule::find($id);

        if (!$data) {
            return response()->json(['message' => 'Module not found'], 404);
        }

        return response()->json($data);
    }

    // UPDATE record
    public function update(Request $request, $id)
    {
        $data = AcademicModule::find($id);

        if (!$data) {
            return response()->json(['message' => 'Module not found'], 404);
        }

        $fields = $request->validate([
            'code'     => 'nullable|string|unique:academic_modules,code,' . $id,
            'name'     => 'nullable|string',
            'credits'  => 'nullable|integer|min:1',
            'faculty'  => 'nullable|string',
            'desc'     => 'nullable|string',
            'prereq'   => 'nullable|string',
        ]);

        $data->update(array_filter($fields, function ($value) {
            return !is_null($value);
        }));

        return response()->json([
            'message' => 'Academic module updated successfully',
            'data'    => $data
        ]);
    }

    // DELETE record
    public function destroy($id)
    {
        $data = AcademicModule::find($id);

        if (!$data) {
            return response()->json(['message' => 'Module not found'], 404);
        }

        $data->delete();

        return response()->json(['message' => 'Academic module deleted successfully']);
    }
}
