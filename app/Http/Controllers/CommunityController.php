<?php

namespace App\Http\Controllers;

use App\Models\Community;
use Illuminate\Http\Request;

class CommunityController extends Controller
{
    // GET all posts
    public function index()
    {
        $posts = Community::with(['student', 'admin'])->latest()->get();
        $posts->map(function ($post) {
            if ($post->is_admin) {
                $mockStudent = new \App\Models\Student();
                $mockStudent->name = $post->admin ? $post->admin->name : 'System Administrator';
                $mockStudent->student_id = 'Admin';
                $mockStudent->faculty = 'Administration';
                $post->setRelation('student', $mockStudent);
            }
            return $post;
        });
        return response()->json($posts);
    }

    // CREATE post
    public function store(Request $request)
    {
        $request->validate([
            'post_content' => 'required|string',
            'description'  => 'nullable|string',
            'image'        => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
        ]);

        $payload = $request->only(['post_content', 'description']);
        $payload['user_id'] = $request->user()->id;
        $payload['is_admin'] = $request->user() instanceof \App\Models\User;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('communities', 'public');
            $payload['image_path'] = $path;
        }

        $data = Community::create($payload);

        if ($data->is_admin) {
            $mockStudent = new \App\Models\Student();
            $mockStudent->name = $request->user()->name;
            $mockStudent->student_id = 'Admin';
            $mockStudent->faculty = 'Administration';
            $data->setRelation('student', $mockStudent);
        } else {
            $data->load('student');
        }

        return response()->json([
            'message' => 'Post created successfully',
            'data' => $data
        ], 201);
    }

    // GET single post
    public function show($id)
    {
        $data = Community::with(['student', 'admin'])->find($id);

        if (!$data) {
            return response()->json(['message' => 'Not found'], 404);
        }

        if ($data->is_admin) {
            $mockStudent = new \App\Models\Student();
            $mockStudent->name = $data->admin ? $data->admin->name : 'System Administrator';
            $mockStudent->student_id = 'Admin';
            $mockStudent->faculty = 'Administration';
            $data->setRelation('student', $mockStudent);
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

        $user = $request->user();
        $isAdmin = $user instanceof \App\Models\User;

        // Check if user is authorized to edit this post (must be owner or admin)
        if (!$isAdmin && $data->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized update request.'], 403);
        }

        $request->validate([
            'post_content' => 'required|string',
            'description'  => 'nullable|string',
        ]);

        $data->update($request->only(['post_content', 'description']));

        return response()->json([
            'message' => 'Updated successfully',
            'data' => $data
        ]);
    }

    // DELETE post
    public function destroy(Request $request, $id)
    {
        $data = Community::find($id);

        if (!$data) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $user = $request->user();
        $isAdmin = $user instanceof \App\Models\User;

        if (!$isAdmin && $data->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized deletion request.'], 403);
        }

        if ($data->image_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($data->image_path);
        }

        $data->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }
}