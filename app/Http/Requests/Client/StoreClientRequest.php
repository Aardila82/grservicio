<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'      => ['required', 'string', 'max:100'],
            'whatsapp'  => ['required', 'string', 'max:20'],
            'email'     => ['sometimes', 'nullable', 'email', 'max:150'],
            'address'   => ['sometimes', 'nullable', 'string'],
            'notes'     => ['sometimes', 'nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'     => 'El nombre del cliente es obligatorio.',
            'whatsapp.required' => 'El número de WhatsApp es obligatorio.',
            'email.email'       => 'El correo no tiene un formato válido.',
        ];
    }
}
