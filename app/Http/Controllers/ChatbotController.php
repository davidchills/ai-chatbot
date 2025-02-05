<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChatMessage;
use Illuminate\Routing\Controller;
use App\Services\OpenAIService;
use Illuminate\Support\Facades\Auth;

class ChatbotController extends Controller {

    protected $openAI;

    public function __construct(OpenAIService $openAI) {
        $this->openAI = $openAI;
    }

    public function handleChat(Request $request) {
        
        try {
            $message = $request->input('message', '');

            /* Add after user tracking is enabled
            $user = Auth::user();
            // Save user message
            ChatMessage::create([
                'user_id' => $user ? $user->id : null,
                'message' => $message,
                'type' => 'user'
            ]);
            */
            // Get AI response
            $botReply = $this->openAI->chat($message);

            /* Add after user tracking is enabled
            // Save bot message
            ChatMessage::create([
                'user_id' => $user ? $user->id : null,
                'message' => $botReply,
                'type' => 'bot'
            ]);       
            */
            return response()->json(['reply' => $botReply]);
        }
        catch (\Exception $e) {
            return response()->json(['error' => 'Internal Server Error', 'message' => $e->getMessage()], 500);
        }  
    }      

    /* Add after user tracking is enabled
    public function getChatHistory() {

        try {
            $user = auth()->user();

            // Get messages (for logged-in users) or last 20 messages for guests
            $messages = ChatMessage::where(function ($query) use ($user) {
                if ($user) {
                    $query->where('user_id', $user->id);
                } 
                else {
                    $query->whereNull('user_id'); // Allow guest users to retrieve chat history
                }
            })
            ->orderBy('created_at', 'asc')
            ->take(20)
            ->get();

            return response()->json($messages, 200);
        } 
        catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load chat history', 'message' => $e->getMessage()], 500);
        }
    }  
    */  
}
