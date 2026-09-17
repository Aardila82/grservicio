<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Setting;
use App\Repositories\Contracts\InvoiceRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class InvoiceService
{
    public function __construct(
        protected InvoiceRepositoryInterface $invoiceRepo,
        protected PdfService $pdfService,
        protected WhatsAppService $whatsAppService,
    ) {}

    public function list(
        string $userId,
        ?string $status = null,
        ?string $search = null,
        ?string $from = null,
        ?string $to = null,
        int $perPage = 15
    ): LengthAwarePaginator {
        return $this->invoiceRepo->getAllForUser($userId, $status, $search, $from, $to, $perPage);
    }

    public function find(string $id, string $userId): Invoice
    {
        return $this->invoiceRepo->findForUser($id, $userId);
    }

    public function create(array $data, string $userId): Invoice
    {
        $invoiceNumber = $this->invoiceRepo->getNextInvoiceNumber($userId);

        return $this->invoiceRepo->create(array_merge($data, [
            'invoice_number' => $invoiceNumber,
            'status'         => 'pending',
        ]));
    }

    public function update(Invoice $invoice, array $data): Invoice
    {
        return $this->invoiceRepo->update($invoice, $data);
    }

    public function delete(Invoice $invoice): bool
    {
        return $this->invoiceRepo->delete($invoice);
    }

    /**
     * Generate PDF and update invoice with path.
     */
    public function generatePdf(Invoice $invoice): Invoice
    {
        $path = $this->pdfService->generate($invoice);

        return $this->invoiceRepo->update($invoice, ['pdf_path' => $path]);
    }

    /**
     * Send invoice via WhatsApp. PDF must exist.
     */
    public function sendViaWhatsApp(Invoice $invoice): array
    {
        if (!$invoice->pdf_path || !$this->pdfService->exists($invoice->pdf_path)) {
            // Auto-generate PDF if it doesn't exist
            $invoice = $this->generatePdf($invoice);
        }

        $fullPath = $this->pdfService->getFullPath($invoice->pdf_path);
        $success  = $this->whatsAppService->sendInvoice($invoice, $fullPath);

        if ($success) {
            $invoice->update(['status' => 'sent']);
        }

        return [
            'success' => $success,
            'invoice' => $invoice->fresh(['client', 'logs']),
        ];
    }
}
