<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScriptCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'role_id'];

    public function scripts()
    {
        return $this->hasMany(Script::class, 'category_id');
    }

    public function role()
    {
        return $this->belongsTo(\Spatie\Permission\Models\Role::class, 'role_id');
    }
}
