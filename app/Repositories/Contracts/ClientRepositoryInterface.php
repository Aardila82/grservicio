<?php

namespace App\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\Client;

interface ClientRepositoryInterface
{
    public function getAllForUser(string $userId, ?string $search = null, int $perPage = 15): LengthAwarePaginator;

    public function findForUser(string $id, string $userId): Client;

    public function create(array $data): Client;

    public function update(Client $client, array $data): Client;

    public function delete(Client $client): bool;

    public function getClientInvoices(string $clientId, string $userId, int $perPage = 15): LengthAwarePaginator;
}
