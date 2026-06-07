<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\PeerMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PeerChatController extends Controller
{
    /**
     * Get list of recent chats with other students.
     * GET /api/v1/peer-chats
     */
    public function recentChats(Request $request)
    {
        $myId = $request->user()->id;

        // Find all student IDs we have exchanged messages with
        $senderIds = PeerMessage::where('receiver_id', $myId)->pluck('sender_id')->toArray();
        $receiverIds = PeerMessage::where('sender_id', $myId)->pluck('receiver_id')->toArray();
        $peerIds = array_unique(array_merge($senderIds, $receiverIds));

        // Exclude ourselves
        $peerIds = array_filter($peerIds, function($id) use ($myId) {
            return $id != $myId;
        });

        if (empty($peerIds)) {
            return response()->json([]);
        }

        // Fetch students and append last message and unread count
        $students = Student::whereIn('id', $peerIds)->get(['id', 'name', 'student_id', 'faculty', 'avatar']);

        $recent = $students->map(function ($student) use ($myId) {
            $lastMsg = PeerMessage::where(function ($q) use ($myId, $student) {
                $q->where('sender_id', $myId)->where('receiver_id', $student->id);
            })->orWhere(function ($q) use ($myId, $student) {
                $q->where('sender_id', $student->id)->where('receiver_id', $myId);
            })->orderBy('created_at', 'desc')->first();

            $unreadCount = PeerMessage::where('sender_id', $student->id)
                ->where('receiver_id', $myId)
                ->where('is_read', false)
                ->count();

            return [
                'peer' => $student,
                'last_message' => $lastMsg,
                'unread_count' => $unreadCount,
                'last_interaction_time' => $lastMsg ? $lastMsg->created_at : null,
            ];
        });

        // Sort by last interaction time desc
        $sorted = $recent->sortByDesc('last_interaction_time')->values();

        return response()->json($sorted);
    }

    /**
     * Search other students by index number (student_id) or name.
     * GET /api/v1/peer-chats/search
     */
    public function search(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:1',
        ]);

        $query = $request->input('query');
        $myId = $request->user()->id;

        $students = Student::where('id', '!=', $myId)
            ->where(function ($q) use ($query) {
                $q->where('student_id', 'like', '%' . $query . '%')
                  ->orWhere('name', 'like', '%' . $query . '%');
            })
            ->take(15)
            ->get(['id', 'name', 'student_id', 'faculty', 'avatar']);

        return response()->json($students);
    }

    /**
     * Get conversation history with a peer student.
     * GET /api/v1/peer-chats/{student_id}/messages
     */
    public function messages(Request $request, string $student_id)
    {
        $myId = $request->user()->id;
        $otherStudent = Student::where('student_id', $student_id)->firstOrFail();
        $otherId = $otherStudent->id;

        $messages = PeerMessage::where(function ($q) use ($myId, $otherId) {
            $q->where('sender_id', $myId)->where('receiver_id', $otherId);
        })->orWhere(function ($q) use ($myId, $otherId) {
            $q->where('sender_id', $otherId)->where('receiver_id', $myId);
        })->orderBy('created_at', 'asc')->get();

        return response()->json([
            'peer'     => $otherStudent,
            'messages' => $messages,
        ]);
    }

    /**
     * Send a message to a peer student (with optional file upload).
     * POST /api/v1/peer-chats/{student_id}/messages
     */
    public function sendMessage(Request $request, string $student_id)
    {
        $request->validate([
            'message' => 'nullable|string|max:5000',
            'file'    => 'nullable|file|max:10240', // 10MB limit
        ]);

        $myId = $request->user()->id;
        $otherStudent = Student::where('student_id', $student_id)->firstOrFail();
        $otherId = $otherStudent->id;

        $messageText = $request->input('message');
        $hasFile = $request->hasFile('file');

        if (!$messageText && !$hasFile) {
            return response()->json(['message' => 'Cannot send an empty message.'], 422);
        }

        $payload = [
            'sender_id'   => $myId,
            'receiver_id' => $otherId,
            'message'     => $messageText,
            'is_read'     => false,
        ];

        if ($hasFile) {
            $file = $request->file('file');
            $path = $file->store('chat_files', 'public');
            $payload['file_path'] = $path;
            $payload['file_name'] = $file->getClientOriginalName();
            $payload['file_type'] = $file->getClientMimeType();
        }

        $message = PeerMessage::create($payload);

        return response()->json($message, 201);
    }

    /**
     * Mark all incoming messages from a peer as read.
     * POST /api/v1/peer-chats/{student_id}/read
     */
    public function markAsRead(Request $request, string $student_id)
    {
        $myId = $request->user()->id;
        $otherStudent = Student::where('student_id', $student_id)->firstOrFail();
        $otherId = $otherStudent->id;

        PeerMessage::where('sender_id', $otherId)
            ->where('receiver_id', $myId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['message' => 'Messages marked as read.']);
    }
}
