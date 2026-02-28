<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nickname' => 'required|string|max:50|unique:users,nickname',
            'email'    => 'required|email|max:70|unique:users,email',
            'password' => 'required|string|max:50',
        ];
    }

    // Opcional: mensagens de erro personalizadas
    public function messages(): array
    {
        return [
            'nickname.unique' => 'Este nickname já está em uso.',
            'email.unique'    => 'Este e-mail já foi cadastrado.',
        ];
    }
}
