<?php

namespace App\Repositories;

use App\Models\Invoice;
use App\Models\Setting;
use App\Repositories\Contracts\InvoiceRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class InvoiceRepository implements InvoiceRepositoryInterface
{
    public function __construct(protected Invoice $model) {}

    public function getAllForUser(
        string $userId,
        ?string $status = null,
        ?string $search = null,
        ?string $from = null,
        ?string $to = null,
        int $perPage = 15
    ): LengthAwarePaginator {
        return $this->model
            ->forUser($userId)
            ->byStatus($status)
            ->withSearch($search)
            ->byDateRange($from, $to)
            ->with('client')
            ->orderByDesc('invoice_date')
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    public function findForUser(string $id, string $userId): Invoice
    {
        return $this->model
            ->forUser($userId)
            ->with(['client', 'logs'])
            ->findOrFail($id);
    }

    public function create(array $data): Invoice
    {
        return $this->model->create($data);
    }

    public function update(Invoice $invoice, array $data): Invoice
    {
        $invoice->update($data);
        return $invoice->fresh(['client', 'logs']);
    }

    public function delete(Invoice $invoice): bool
    {
        return $invoice->delete();
    }

    public function getNextInvoiceNumber(string $userId): string
    {
        $settings = Setting::first();
        $prefix   = $settings?->invoice_prefix ?? 'FAC';
        $year     = now()->format('Y');

        $last = $this->model
            ->forUser($userId)
            ->whereYear('created_at', $year)
            ->withTrashed()
            ->count();

        $sequence = str_pad($last + 1, 4, '0', STR_PAD_LEFT);

        return "{$prefix}-{$year}-{$sequence}";
    }
}
