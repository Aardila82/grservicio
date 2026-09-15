<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\StoreClientRequest;
use App\Http\Requests\Client\UpdateClientRequest;
use App\Http\Resources\ClientResource;
use App\Repositories\Contracts\ClientRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ClientController extends Controller
{
    public function __construct(protected ClientRepositoryInterface $clientRepo) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $clients = $this->clientRepo->getAllForUser(
            userId:  $request->user()->id,
            search:  $request->query('search'),
            perPage: (int) $request->query('per_page', 15),
        );

        return ClientResource::collection($clients);
    }

    public function store(StoreClientRequest $request): JsonResponse
    {
        $client = $this->clientRepo->create($request->validated());

        return response()->json([
            'message' => 'Cliente creado exitosamente.',
            'data'    => new ClientResource($client),
        ], 201);
    }

    public function show(Request $request, string $id): JsonResponse
    {
        $client = $this->clientRepo->findForUser($id, $request->user()->id);

        return response()->json(['data' => new ClientResource($client)]);
    }

    public function update(UpdateClientRequest $request, string $id): JsonResponse
    {
        $client  = $this->clientRepo->findForUser($id, $request->user()->id);
        $updated = $this->clientRepo->update($client, $request->validated());

        return response()->json([
            'message' => 'Cliente actualizado.',
            'data'    => new ClientResource($updated),
        ]);
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        $client = $this->clientRepo->findForUser($id, $request->user()->id);
        $this->clientRepo->delete($client);

        return response()->json(['message' => 'Cliente eliminado.']);
    }

    public function invoices(Request $request, string $id): JsonResponse
    {
        $invoices = $this->clientRepo->getClientInvoices(
            clientId: $id,
            userId:   $request->user()->id,
            perPage:  (int) $request->query('per_page', 10),
        );

        return response()->json($invoices);
    }
}
