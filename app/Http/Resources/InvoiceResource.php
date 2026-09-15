<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'invoice_number'     => $this->invoice_number,
            'service'            => $this->service,
            'value'              => (float) $this->value,
            'is_paid'            => (bool) $this->is_paid,
            'pending_amount'     => $this->pending_amount ? (float) $this->pending_amount : null,
            'formatted_value'    => $this->formatted_value,
            'notes'              => $this->notes,
            'invoice_date'       => $this->invoice_date?->toDateString(),
            'pdf_path'           => $this->pdf_path,
            'public_pdf_url'     => $this->pdf_path ? route('invoices.public.view', ['invoice' => $this->id]) : null,
            'status'             => $this->status,
            'status_label'       => $this->status_label,
            'whatsapp_sent_at'   => $this->whatsapp_sent_at?->toISOString(),
            'client'             => new ClientResource($this->whenLoaded('client')),
            'logs'               => $this->whenLoaded('logs'),
            'created_at'         => $this->created_at?->toISOString(),
            'updated_at'         => $this->updated_at?->toISOString(),
        ];
    }
}
