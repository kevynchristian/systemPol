<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AulaRegistro extends Model
{
    use HasFactory;

    protected $fillable = [
        'aula_id',
        'instrutor_id',
        'aluno_id',
        'status',
        'observacao'
    ];

    public function aula()
    {
        return $this->belongsTo(Aula::class, 'aula_id')->withTrashed();
    }

    public function instrutor()
    {
        return $this->belongsTo(User::class, 'instrutor_id');
    }

    public function aluno()
    {
        return $this->belongsTo(User::class, 'aluno_id');
    }
}
