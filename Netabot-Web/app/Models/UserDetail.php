<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use function Laravel\Prompts\table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class UserDetail extends Model
{

    use HasFactory;
    protected $fillable = ['id_user', 'username', 'fullname', 'roles'];
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }

    public function user_chat()
{
    return $this->hasMany(UserChat::class, 'id_user', 'id');
}
}

