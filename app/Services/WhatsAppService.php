<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceLog;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    private string $serviceUrl;
    private string $apiKey;

    public function __construct()
    {
        $this->serviceUrl = rtrim(config('services.whatsapp.url', 'http://localhost:3000'), '/');
        $this->apiKey     = config('services.whatsapp.key', '');
    }

    /**
     * Send an invoice PDF via the WhatsApp microservice.
     */
    public function sendInvoice(Invoice $invoice, string $pdfFullPath): bool
    {
        $invoice->load(['client']);
        $settings = Setting::first();

        $message = $settings?->whatsapp_message
            ?? 'Hola. Adjuntamos la factura correspondiente al servicio realizado. Muchas gracias.';

        $phone = $this->normalizePhone($invoice->client->whatsapp);

        try {
            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
            ])
            ->timeout(30)
            ->attach('pdf', file_get_contents($pdfFullPath), basename($pdfFullPath))
            ->post("{$this->serviceUrl}/send", [
                'phone'   => $phone,
                'message' => $message,
            ]);

            if ($response->successful()) {
                $invoice->update([
                    'status'            => 'sent',
                    'whatsapp_sent_at'  => now(),
                ]);

                InvoiceLog::create([
                    'invoice_id' => $invoice->id,
                    'event'      => 'sent',
                    'message'    => "WhatsApp enviado a {$phone}",
                    'sent_at'    => now(),
                    'created_at' => now(),
                ]);

                return true;
            }

            $errorDetail = $response->body();
            $this->logError($invoice, "HTTP {$response->status()}: {$errorDetail}");

            return false;

        } catch (\Exception $e) {
            Log::error('WhatsApp service error', [
                'invoice_id' => $invoice->id,
                'error'      => $e->getMessage(),
            ]);

            $this->logError($invoice, $e->getMessage());

            return false;
        }
    }

    /**
     * Check if the WhatsApp microservice is ready.
     */
    public function getStatus(): array
    {
        try {
            $response = Http::withHeaders(['x-api-key' => $this->apiKey])
                ->timeout(5)
                ->get("{$this->serviceUrl}/status");

            return $response->json() ?? ['status' => 'unknown'];
        } catch (\Exception $e) {
            return ['status' => 'offline', 'error' => $e->getMessage()];
        }
    }

    private function logError(Invoice $invoice, string $detail): void
    {
        $invoice->update(['status' => 'error']);

        InvoiceLog::create([
            'invoice_id'   => $invoice->id,
            'event'        => 'error',
            'message'      => 'Error al enviar por WhatsApp',
            'error_detail' => $detail,
            'created_at'   => now(),
        ]);
    }

    /**
     * Normalize phone number to international format without +.
     */
    private function normalizePhone(string $phone): string
    {
        $cleaned = preg_replace('/[^0-9]/', '', $phone);

        // If it starts with 0, remove leading zero
        if (str_starts_with($cleaned, '0')) {
            $cleaned = ltrim($cleaned, '0');
        }

        // Add Colombia country code if not present
        if (!str_starts_with($cleaned, '57') && strlen($cleaned) === 10) {
            $cleaned = '57' . $cleaned;
        }

        return $cleaned;
    }
}
