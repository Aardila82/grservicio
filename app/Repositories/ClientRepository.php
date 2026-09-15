<?php

namespace App\Repositories;

use App\Models\Client;
use App\Repositories\Contracts\ClientRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ClientRepository implements ClientRepositoryInterface
{
    public function __construct(protected Client $model) {}

    public function getAllForUser(string $userId, ?string $search = null, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->forUser($userId)
            ->search($search)
            ->withCount('invoices')
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function findForUser(string $id, string $userId): Client
    {
        return $this->model
            ->forUser($userId)
            ->with('invoices')
            ->findOrFail($id);
    }

    public function create(array $data): Client
    {
        return $this->model->create($data);
    }

    public function update(Client $client, array $data): Client
    {
        $client->update($data);
        return $client->fresh();
    }

    public function delete(Client $client): bool
    {
        return $client->delete();
    }

    public function getClientInvoices(string $clientId, string $userId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->forUser($userId)
            ->findOrFail($clientId)
            ->invoices()
            ->with('client')
            ->orderByDesc('invoice_date')
            ->paginate($perPage);
    }
}
