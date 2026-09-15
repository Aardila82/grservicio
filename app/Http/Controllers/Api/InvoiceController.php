<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Invoice\StoreInvoiceRequest;
use App\Http\Requests\Invoice\UpdateInvoiceRequest;
use App\Http\Resources\InvoiceResource;
use App\Services\InvoiceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;

class InvoiceController extends Controller
{
    public function __construct(protected InvoiceService $invoiceService) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $invoices = $this->invoiceService->list(
            userId:  $request->user()->id,
            status:  $request->query('status'),
            search:  $request->query('search'),
            from:    $request->query('from'),
            to:      $request->query('to'),
            perPage: (int) $request->query('per_page', 15),
        );

        return InvoiceResource::collection($invoices);
    }

    public function store(StoreInvoiceRequest $request): JsonResponse
    {
        $invoice = $this->invoiceService->create(
            $request->validated(),
            $request->user()->id
        );

        return response()->json([
            'message' => 'Factura creada exitosamente.',
            'data'    => new InvoiceResource($invoice->load('client')),
        ], 201);
    }

    public function show(Request $request, string $id): JsonResponse
    {
        $invoice = $this->invoiceService->find($id, $request->user()->id);

        return response()->json(['data' => new InvoiceResource($invoice)]);
    }

    public function update(UpdateInvoiceRequest $request, string $id): JsonResponse
    {
        $invoice = $this->invoiceService->find($id, $request->user()->id);
        $updated = $this->invoiceService->update($invoice, $request->validated());

        return response()->json([
            'message' => 'Factura actualizada.',
            'data'    => new InvoiceResource($updated),
        ]);
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        $invoice = $this->invoiceService->find($id, $request->user()->id);
        $this->invoiceService->delete($invoice);

        return response()->json(['message' => 'Factura eliminada.']);
    }

    public function generatePdf(Request $request, string $id): JsonResponse
    {
        $invoice = $this->invoiceService->find($id, $request->user()->id);
        $updated = $this->invoiceService->generatePdf($invoice);

        return response()->json([
            'message'  => 'PDF generado exitosamente.',
            'pdf_path' => $updated->pdf_path,
            'data'     => new InvoiceResource($updated),
        ]);
    }

    public function downloadPdf(Request $request, string $id)
    {
        $invoice = $this->invoiceService->find($id, $request->user()->id);

        if (!$invoice->pdf_path || !Storage::disk('local')->exists($invoice->pdf_path)) {
            return response()->json(['message' => 'PDF no generado aún.'], 404);
        }

        $fullPath = Storage::disk('local')->path($invoice->pdf_path);
        $filename = basename($invoice->pdf_path);

        return response()->download($fullPath, $filename, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    public function publicView(string $id)
    {
        // Notice we do NOT check user_id here because it's a signed public route!
        $invoice = \App\Models\Invoice::findOrFail($id);

        if (!$invoice->pdf_path || !Storage::disk('local')->exists($invoice->pdf_path)) {
            return response()->json(['message' => 'El PDF de esta factura no existe o aún no ha sido generado.'], 404);
        }

        $fullPath = Storage::disk('local')->path($invoice->pdf_path);
        
        // Return inline so it opens in the browser instead of downloading directly
        return response()->file($fullPath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.basename($invoice->pdf_path).'"'
        ]);
    }

    public function sendWhatsApp(Request $request, string $id): JsonResponse
    {
        $invoice = $this->invoiceService->find($id, $request->user()->id);
        $result  = $this->invoiceService->sendViaWhatsApp($invoice);

        if ($result['success']) {
            return response()->json([
                'message' => 'Factura enviada por WhatsApp exitosamente.',
                'data'    => new InvoiceResource($result['invoice']),
            ]);
        }

        return response()->json([
            'message' => 'Error al enviar por WhatsApp. Revisa los logs.',
            'data'    => new InvoiceResource($result['invoice']),
        ], 422);
    }

    public function logs(Request $request, string $id): JsonResponse
    {
        $invoice = $this->invoiceService->find($id, $request->user()->id);

        return response()->json([
            'data' => $invoice->logs()->orderByDesc('created_at')->get(),
        ]);
    }
}
