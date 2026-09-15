<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use App\Http\Resources\InvoiceResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(protected DashboardService $dashboardService) {}

    public function stats(Request $request): JsonResponse
    {
        $stats = $this->dashboardService->getStats($request->user()->id);

        return response()->json(['data' => $stats]);
    }

    public function recentInvoices(Request $request): JsonResponse
    {
        $invoices = $this->dashboardService->getRecentInvoices(
            $request->user()->id,
            (int) $request->query('limit', 10)
        );

        return response()->json([
            'data' => InvoiceResource::collection($invoices),
        ]);
    }
}
