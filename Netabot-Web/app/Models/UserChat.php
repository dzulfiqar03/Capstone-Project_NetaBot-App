<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserChat extends Model
{
    use HasFactory;
    protected $table = 'user_chat';
    protected $fillable = ['id_user', 'chat', 'bot_response', 'session_key'];
    public function user_detail()
    {
        return $this->belongsTo(UserDetail::class, 'id_user', 'id');
    }
}
