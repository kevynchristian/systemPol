<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AuthenticateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Permite que qualquer um tente autenticar
    }

    public function rules(): array
    {
        return [
            'nickname' => 'required|string',
            'password' => 'required|string', // Nome do campo alterado de 'senha'
        ];
    }
}
