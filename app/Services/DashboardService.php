<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Client;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function getStats(string $userId): array
    {
        $invoiceQuery = Invoice::forUser($userId);

        return [
            'total_invoices'    => (clone $invoiceQuery)->count(),
            'invoices_sent'     => (clone $invoiceQuery)->byStatus('sent')->count(),
            'invoices_pending'  => (clone $invoiceQuery)->byStatus('pending')->count(),
            'invoices_error'    => (clone $invoiceQuery)->byStatus('error')->count(),
            'total_clients'     => Client::forUser($userId)->count(),
            'total_value_month' => (clone $invoiceQuery)
                ->whereMonth('invoice_date', now()->month)
                ->whereYear('invoice_date', now()->year)
                ->sum('value'),
            'total_value_all'   => (clone $invoiceQuery)->sum('value'),
        ];
    }

    public function getRecentInvoices(string $userId, int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return Invoice::forUser($userId)
            ->with('client')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }
}
