<?php
namespace App\Http\Controllers;

use App\Models\AccountMethod;
use App\Http\Services\AccountMethod\{
IndexAccountMethodService,
ShowAccountMethodService,
StoreAccountMethodService,
UpdateAccountMethodService,
DestroyAccountMethodService,
ToggleAccountMethodService
};
use Illuminate\Http\{
Request,
JsonResponse
};

class AccountMethodController extends Controller
{

    public function index(Request $request): JsonResponse
    {
        return IndexAccountMethodService::execute($request);
    }

    public function show(AccountMethod $account_method): JsonResponse
    {
        return ShowAccountMethodService::execute($account_method);
    }

    public function store(Request $request): JsonResponse
    {
        return StoreAccountMethodService::execute($request);
    }

    public function update(Request $request, AccountMethod $account_method): JsonResponse
    {
        return UpdateAccountMethodService::execute($request, $account_method);
    }

    public function destroy(AccountMethod $account_method): JsonResponse
    {
        return DestroyAccountMethodService::execute($account_method);
    }

    public function toggleStatus(AccountMethod $account_method): JsonResponse
    {
        return ToggleAccountMethodService::execute($account_method);
    }

}