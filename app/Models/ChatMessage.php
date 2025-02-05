<?php

namespace App\Models;


use Illuminate\Foundation\Auth\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ChatMessage extends Model {
    use HasFactory;

    protected $fillable = ['user_id', 'message', 'type'];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
