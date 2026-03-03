<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromocaoLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'promoter_id',
        'old_role_id',
        'new_role_id',
        'description'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function promoter()
    {
        return $this->belongsTo(User::class, 'promoter_id');
    }

    public function oldRole()
    {
        return $this->belongsTo(Role::class, 'old_role_id');
    }

    public function newRole()
    {
        return $this->belongsTo(Role::class, 'new_role_id');
    }
}
