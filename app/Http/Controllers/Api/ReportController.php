<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\Student;
use App\Models\Community;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    /**
     * Store a new report (submitted by student).
     * POST /api/v1/reports
     */
    public function store(Request $request)
    {
        if ($request->input('reported_post_id')) {
            $post = Community::find($request->input('reported_post_id'));
            if ($post && ($post->is_admin || !Student::where('id', $post->user_id)->exists())) {
                $request->merge(['reported_student_id' => null]);
            }
        }

        $request->validate([
            'reported_student_id' => 'nullable|exists:students,id',
            'reported_post_id'    => 'nullable|exists:communities,id',
            'reason'              => 'required|string|max:255',
            'details'             => 'nullable|string|max:5000',
            'screenshot'          => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,heic,heif|max:10240', // 10MB
        ]);

        if (!$request->input('reported_student_id') && !$request->input('reported_post_id')) {
            return response()->json(['message' => 'You must report either a student or a community post.'], 422);
        }

        $payload = [
            'reporter_id'         => $request->user()->id,
            'reported_student_id' => $request->input('reported_student_id'),
            'reported_post_id'    => $request->input('reported_post_id'),
            'reason'              => $request->input('reason'),
            'details'             => $request->input('details'),
            'status'              => 'pending',
        ];

        if ($request->hasFile('screenshot')) {
            $path = $request->file('screenshot')->store('reports', 'public');
            $payload['screenshot_path'] = $path;
        }

        $report = Report::create($payload);

        return response()->json([
            'message' => 'Report submitted successfully.',
            'report'  => $report
        ], 201);
    }

    /**
     * List all reports (admin only).
     * GET /api/v1/admin/reports
     */
    public function index(Request $request)
    {
        $reports = Report::with(['reporter', 'reportedStudent', 'reportedPost', 'reportedPost.student'])
            ->latest()
            ->get();

        return response()->json($reports);
    }

    /**
     * Execute admin moderation action on a report.
     * POST /api/v1/admin/reports/{id}/action
     */
    public function takeAction(Request $request, string $id)
    {
        $request->validate([
            'action'          => 'required|string|in:warn,delete_post,ban',
            'warning_message' => 'required_if:action,warn|nullable|string|max:1000',
        ]);

        $report = Report::findOrFail($id);
        $action = $request->input('action');

        // Locate targeted student
        $targetStudent = null;
        if ($report->reported_student_id) {
            $targetStudent = Student::find($report->reported_student_id);
        } elseif ($report->reported_post_id) {
            $post = Community::find($report->reported_post_id);
            if ($post && !$post->is_admin) {
                $targetStudent = Student::find($post->user_id);
            }
        }

        if ($action === 'warn') {
            if (!$targetStudent) {
                return response()->json(['message' => 'No student associated with this report to warn.'], 422);
            }
            $targetStudent->increment('warnings_count');
            $targetStudent->update([
                'warning_message' => $request->input('warning_message'),
            ]);

            $report->update([
                'status'       => 'resolved',
                'action_taken' => 'warned',
            ]);

            return response()->json(['message' => 'Student has been warned successfully.']);
        }

        if ($action === 'delete_post') {
            if (!$report->reported_post_id) {
                return response()->json(['message' => 'No post associated with this report to delete.'], 422);
            }
            $post = Community::find($report->reported_post_id);
            if ($post) {
                // Delete image if exists
                if ($post->image_path) {
                    Storage::disk('public')->delete($post->image_path);
                }
                $post->delete();
            }

            $report->update([
                'status'       => 'resolved',
                'action_taken' => 'deleted',
            ]);

            return response()->json(['message' => 'Post has been deleted successfully.']);
        }

        if ($action === 'ban') {
            if (!$targetStudent) {
                return response()->json(['message' => 'No student associated with this report to ban.'], 422);
            }
            $targetStudent->update([
                'is_banned' => true,
            ]);

            $report->update([
                'status'       => 'resolved',
                'action_taken' => 'banned',
            ]);

            return response()->json(['message' => 'Student has been banned successfully.']);
        }

        return response()->json(['message' => 'Invalid action.'], 400);
    }
}
