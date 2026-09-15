<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceLog;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PdfService
{
    /**
     * Generate the PDF for an invoice and store it.
     * Returns the relative storage path.
     */
    public function generate(Invoice $invoice): string
    {
        $invoice->load(['client']);

        $settings = Setting::first();

        $logoBase64 = $this->encodeLogoAsBase64($settings?->logo_path);

        $pdf = Pdf::loadView('pdf.invoice', [
            'invoice'     => $invoice,
            'client'      => $invoice->client,
            'settings'    => $settings,
            'logoBase64'  => $logoBase64,
        ])->setPaper('a4');

        $filename  = Str::slug($invoice->invoice_number) . '.pdf';
        $directory = 'invoices';
        $path      = "{$directory}/{$filename}";

        Storage::disk('local')->put($path, $pdf->output());

        // Log the event
        InvoiceLog::create([
            'invoice_id' => $invoice->id,
            'event'      => 'generated',
            'message'    => "PDF generado: {$filename}",
            'created_at' => now(),
        ]);

        return $path;
    }

    /**
     * Get the full disk path for a stored PDF.
     */
    public function getFullPath(string $relativePath): string
    {
        return Storage::disk('local')->path($relativePath);
    }

    /**
     * Check if PDF exists on disk.
     */
    public function exists(string $relativePath): bool
    {
        return Storage::disk('local')->exists($relativePath);
    }

    private function encodeLogoAsBase64(?string $logoPath): ?string
    {
        if (!$logoPath || !Storage::disk('local')->exists($logoPath)) {
            return null;
        }

        $content  = Storage::disk('local')->get($logoPath);
        $mimeType = Storage::disk('local')->mimeType($logoPath);

        return 'data:' . $mimeType . ';base64,' . base64_encode($content);
    }
}
