<?php

namespace App\Http\Controllers;

use Illuminate\Http\{Request, JsonResponse};
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

use App\Http\Requests\Client\{
    StoreClientRequest,
    UpdateClientRequest
};
use App\Http\Services\Clients\{
    getClientService,
    StoreClientService,
    IndexClientService,
    UpdateClientService,
};

use App\Models\Personas;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        return IndexClientService::execute($request);
    }

    public function store(StoreClientRequest $request): JsonResponse
    {
        return StoreClientService::execute($request);
    }

    public function show(Request $request, string $id): JsonResponse
    {
        return getClientService::index($request, $id);
    }

    public function update(UpdateClientRequest $request, String $id): JsonResponse
    {
        return UpdateClientService::execute($request, $id);
    }

    public function destroy(Request $request): JsonResponse
    {
        $user = User::where([
            ['uuid', $request->uuid],
            ['users.id', '>', 1],
            ['role_id', 3]
        ])->first();

        $persona = Personas::where('id', $user->persona_id)->first();
        if (!$user || !$persona) return response()->json(['error' => 'Cliente no encontrado'], 404);

        if ($persona->image) {
            $decryptedPath = Crypt::decryptString($persona->image);
            if (Storage::disk('private')->exists($decryptedPath)) {
                Storage::disk('private')->delete($decryptedPath);
            }
        }

        $user->delete();
        $persona->delete();
        return response()->json(204);
    }
}
