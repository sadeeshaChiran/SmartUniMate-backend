<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;


class ChatController extends Controller
{


    public function send(Request $request)
    {

        $request->validate([
            'message' => 'required|string|max:2000',
        ]);


        $student     = Student::find($request->user()->id);
        // $student     = Student::find(1);
        $userMessage = $request->input('message');

        ChatMessage::create([
            'student_id' => $student->id,
            'role'       => 'user',
            'message'    => $userMessage,
        ]);


        $history = ChatMessage::where('student_id', $student->id)
            ->orderBy('created_at', 'asc')
            ->take(10)
            ->get()
            ->map(fn($msg) => [
                'role'    => $msg->role === 'bot' ? 'assistant' : $msg->role,
                'content' => $msg->message,
            ])
            ->toArray();

        $botUrl = config('services.bot.url', 'http://localhost:8001');


        try {
            $botResponse = Http::timeout(30)
                ->withHeaders([
                    'Content-Type'  => 'application/json',
                    'X-Bot-Secret'  => config('services.bot.secret'),
                ])
                ->post("{$botUrl}/chat", [
                    'message'    => $userMessage,
                    'student_id' => $student->id,
                    'name'       => $student->name,
                    'history'    => $history,
                ]);

            if ($botResponse->failed()) {
                Log::error('Bot service error', [
                    'status' => $botResponse->status(),
                    'body'   => $botResponse->body(),
                ]);
                return response()->json([
                    'message' => 'AI service is currently unavailable. Please try again later.',
                ], 503);
            }

            $aiReply = $botResponse->json('reply') ?? 'No response received.';
        } catch (\Exception $e) {
            Log::error('Bot connection failed', ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Could not connect to AI service. Please try again.',
            ], 503);
        }

        ChatMessage::create([
            'student_id' => $student->id,
            'role'       => 'assistant',
            'message'    => $aiReply,
        ]);

        return response()->json([
            'reply' => $this->formatMessage($aiReply),
        ]);
    }

    private function formatMessage($text)
    {
        // Convert markdown-style formatting into HTML-safe output
        $text = e($text); // escape HTML first

        // line breaks
        $text = nl2br($text);

        // bold (**text**)
        $text = preg_replace('/\*\*(.*?)\*\*/s', '<b>$1</b>', $text);

        // bullets (* item)
        $text = preg_replace('/^\* (.*)$/m', '• $1', $text);

        // dash bullets
        $text = preg_replace('/^- (.*)$/m', '• $1', $text);

        return $text;
    }


    public function history(Request $request)
    {
        $messages = ChatMessage::where('student_id', $request->user()->id)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'messages' => $messages,
        ]);
    }

    public function clearHistory(Request $request)
    {
        ChatMessage::where('student_id', $request->user()->id)->delete();

        return response()->json(['message' => 'Chat history cleared.']);
    }
}
