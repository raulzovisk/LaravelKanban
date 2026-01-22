<?php
// app/Http/Requests/UpdateBoardRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBoardRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'color' => ['nullable', 'string', 'max:7', 'regex:/^#([A-Fa-f0-9]{6})$/'],
            'is_public' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome do quadro é obrigatório.',
            'name.max' => 'O nome do quadro não pode ter mais de 255 caracteres.',
            'color.regex' => 'A cor deve estar no formato hexadecimal (#000000).',
        ];
    }
}
