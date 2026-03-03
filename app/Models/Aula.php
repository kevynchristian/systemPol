<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Aula extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'required_permission',
        'prerequisite_id'
    ];

    /**
     * The roles (Patentes) that are allowed to take this class.
     */
    public function roles()
    {
        return $this->belongsToMany(\Spatie\Permission\Models\Role::class, 'aula_role', 'aula_id', 'role_id');
    }

    /**
     * The prerequisite class that must be completed before taking this one.
     */
    public function prerequisite()
    {
        return $this->belongsTo(Aula::class, 'prerequisite_id');
    }
}
