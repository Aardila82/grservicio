<?php

namespace App\Http\Requests\Invoice;

use Illuminate\Foundation\Http\FormRequest;

class StoreInvoiceRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'client_id'    => ['required', 'uuid', 'exists:clients,id'],
            'service'      => ['required', 'string'],
            'value'          => ['required', 'numeric', 'min:0'],
            'is_paid'        => ['boolean'],
            'pending_amount' => ['nullable', 'numeric', 'min:0'],
            'notes'          => ['sometimes', 'nullable', 'string'],
            'invoice_date' => ['required', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'client_id.required'    => 'Debe seleccionar un cliente.',
            'client_id.exists'      => 'El cliente seleccionado no existe.',
            'service.required'      => 'El servicio realizado es obligatorio.',
            'value.required'        => 'El valor es obligatorio.',
            'value.numeric'         => 'El valor debe ser un número.',
            'value.min'             => 'El valor no puede ser negativo.',
            'invoice_date.required' => 'La fecha de la factura es obligatoria.',
        ];
    }
}
