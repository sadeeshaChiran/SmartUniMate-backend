<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatController extends Controller
{
    /**
     * Send a message and get an AI response.
     * POST /api/chat
     */
    public function send(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $student = $request->user();
        $userMessage = $request->input('message');

        // Save the student's message
        ChatMessage::create([
            'student_id' => $student->id,
            'role'       => 'user',
            'message'    => $userMessage,
        ]);

        // Fetch last 10 messages for context
        $history = ChatMessage::where('student_id', $student->id)
            ->orderBy('created_at', 'asc')
            ->take(10)
            ->get()
            ->map(fn($msg) => [
                'role'    => $msg->role,
                'content' => $msg->message,
            ])
            ->toArray();

        // Call Anthropic Claude API
        $response = Http::withHeaders([
            'x-api-key'         => config('services.anthropic.key'),
            'anthropic-version' => '2023-06-01',
            'Content-Type'      => 'application/json',
        ])->post('https://api.anthropic.com/v1/messages', [
            'model'      => 'claude-3-5-haiku-20241022',
            'max_tokens' => 1024,
            'system'     => "You are SmartUniMate, a helpful AI assistant for university students. 
                             You help students with academic questions, study tips, university life, 
                             and general knowledge. Be friendly, concise, and encouraging.
                             The student's name is {$student->name}.",
            'messages'   => $history,
        ]);

        if ($response->failed()) {
            return response()->json([
                'message' => 'AI service is currently unavailable. Please try again later.',
            ], 503);
        }

        $aiReply = $response->json('content.0.text');

        // Save the AI's response
        ChatMessage::create([
            'student_id' => $student->id,
            'role'       => 'assistant',
            'message'    => $aiReply,
        ]);

        return response()->json([
            'reply' => $aiReply,
        ]);
    }

    /**
     * Get chat history for the authenticated student.
     * GET /api/chat/history
     */
    public function history(Request $request)
    {
        $messages = ChatMessage::where('student_id', $request->user()->id)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'messages' => $messages,
        ]);
    }

    /**
     * Clear chat history.
     * DELETE /api/chat/history
     */
    public function clearHistory(Request $request)
    {
        ChatMessage::where('student_id', $request->user()->id)->delete();

        return response()->json(['message' => 'Chat history cleared']);
    }
}
