<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserChat extends Model
{
    protected $table = 'user_chat';
    protected $fillable = ['id_user', 'chat', 'bot_response', 'session_key'];
    public function user_detail()
    {
        return $this->belongsTo(UserDetail::class, 'id_user', 'id');
    }
}
