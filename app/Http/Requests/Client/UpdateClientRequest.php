<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClientRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'      => ['sometimes', 'string', 'max:100'],
            'whatsapp'  => ['sometimes', 'string', 'max:20'],
            'email'     => ['sometimes', 'nullable', 'email', 'max:150'],
            'address'   => ['sometimes', 'nullable', 'string'],
            'notes'     => ['sometimes', 'nullable', 'string'],
        ];
    }
}
