<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use function Laravel\Prompts\table;

class UserDetail extends Model
{

    protected $fillable = ['id_user', 'username', 'fullname', 'roles'];
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }
}
