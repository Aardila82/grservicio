<?php

namespace App\Http\Requests\Invoice;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInvoiceRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'client_id'    => ['sometimes', 'uuid', 'exists:clients,id'],
            'service'      => ['sometimes', 'string'],
            'value'          => ['sometimes', 'numeric', 'min:0'],
            'is_paid'        => ['sometimes', 'boolean'],
            'pending_amount' => ['nullable', 'numeric', 'min:0'],
            'notes'          => ['sometimes', 'nullable', 'string'],
            'invoice_date'   => ['sometimes', 'date'],
            'status'         => ['sometimes', 'string', 'in:pending,sent,error'],
        ];
    }
}
