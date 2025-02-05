<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatbotController;

Route::get('/', function () {
    return view('chat');
});

Route::get('/chat', function () {
    return view('chat');
});

// Implement later after we start tracking users.
//Route::get('/chat/history', [ChatbotController::class, 'getChatHistory']);

Route::post('/chat', [ChatbotController::class, 'handleChat']);