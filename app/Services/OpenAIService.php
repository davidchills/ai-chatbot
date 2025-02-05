<?php

namespace App\Services;

use OpenAI;
use Illuminate\Support\Facades\Log;

class OpenAIService{
    protected $client;

    public function __construct() {
        $this->client = OpenAI::client(env('OPENAI_API_KEY'));
    }

    public function chat($message) {
		try {
			$response = $this->client->chat()->create([
				'model' => 'gpt-4',
				'messages' => [
                    ['role' => 'system', 'content' => 'You are a helpful assistant.'],
                    ['role' => 'user', 'content' => $message],					
				],
				'max_tokens' => 100,
			]);
			Log::info('OpenAI Response:', ['response' => $response]);
			return trim($response->choices[0]->message->content ?? 'Error: No valid response from AI.');
		}
		catch(\Exception $e) {
			Log::error('Error in OpenAI API Call: ' . $e->getMessage());
			return 'Error connecting to AI: ' . $e->getMessage();
		} 
	}
}
?>