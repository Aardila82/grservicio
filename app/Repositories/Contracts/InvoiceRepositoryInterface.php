<?php

namespace App\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\Invoice;

interface InvoiceRepositoryInterface
{
    public function getAllForUser(
        string $userId,
        ?string $status = null,
        ?string $search = null,
        ?string $from = null,
        ?string $to = null,
        int $perPage = 15
    ): LengthAwarePaginator;

    public function findForUser(string $id, string $userId): Invoice;

    public function create(array $data): Invoice;

    public function update(Invoice $invoice, array $data): Invoice;

    public function delete(Invoice $invoice): bool;

    public function getNextInvoiceNumber(string $userId): string;
}
