<?php

use App\Http\Controllers\ApplyController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuthMenuController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TokenController;
use App\Http\Controllers\AvatarController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\FinancingController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\StaticsController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\LotesController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\PaymentsController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\AccountMethodController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\CobrosController;

Route::post('/sanctum/token', TokenController::class);

Route::prefix('guest')->group(function () {
    Route::get('/vehicles/{type}', [GuestController::class, 'vehicles']);
    Route::get('/vehicle/{id}', [GuestController::class, 'vehicle']);
    Route::post('/apply', [GuestController::class, 'apply']);
});

Route::middleware(['auth:sanctum'])->group(function () {

    Route::prefix('users')->group(function () {
        Route::get('/auth', AuthController::class);
        Route::get('/show', [AuthController::class, 'show']);
        Route::put('/update/{id}', [AuthController::class, 'edit']);
        Route::get('/auth-menu', AuthMenuController::class);
    });

    Route::prefix('home-client')->group(function () {
        Route::get('/data', [GuestController::class, 'dashboard']);
        Route::get('/account-methods', [GuestController::class, 'getAccountMethods']);
        Route::post('/payment/{financing_id}', [GuestController::class, 'payment']);
        Route::post('/mantenimient/{id}', [GuestController::class, 'mantenimient']);
        Route::get('/doc/guest/{path}', [FileController::class, 'indexClient'])->where('path', '.*');
    });

    // only Admin or superAdmin
    Route::middleware(['admin'])->group(function () {

        Route::get('/documents/{path}', [FileController::class, 'index'])->where('path', '.*');

        Route::prefix('users')->group(function () {
            Route::get('/', [UserController::class, 'index']);
            Route::get('/min/{role}', [UserController::class, 'indexMin']);
            Route::get('/{uuid}', [UserController::class, 'show']);
            Route::post('/', [UserController::class, 'store']);
            Route::post('/{uuid}', [UserController::class, 'update']);
            Route::post('/auth/avatar', [AvatarController::class, 'store']);
        });

        Route::prefix('clients')->group(function () {
            Route::get('/{id}', [ClientController::class, 'show']);
            Route::get('/', [ClientController::class, 'index']);
            Route::post('/', [ClientController::class, 'store']);
            Route::put('/{id}', [ClientController::class, 'update']);
        });

        Route::prefix('menus')->group(function () {
            Route::get('/', [MenuController::class, 'index']);
            Route::get('/children/{menuId}', [MenuController::class, 'children']);
            Route::post('/', [MenuController::class, 'store']);
            Route::put('/{menu}', [MenuController::class, 'update']);
        });

        Route::prefix('roles')->group(function () {
            Route::get('/helperTables', fn() => response()->json([
                "roles" => \App\Models\Role::select('id', 'name')->where('id', '!=', '3')->get()
            ], 200));
            Route::get('/{role}', [RoleController::class, 'show']);
            Route::get('/', [RoleController::class, 'index']);
            Route::post('/', [RoleController::class, 'store']);
            Route::put('/{role}', [RoleController::class, 'update']);
        });

        Route::prefix('statics')->group(function () {
            Route::get('/admin/counted', [StaticsController::class, 'index']);
            Route::get('/admin/payments', [StaticsController::class, 'payments']);
            Route::get('/admin/financings', [StaticsController::class, 'financing']);
        });

        Route::prefix('vehicles')->group(function () {
            Route::get('/', [VehicleController::class, 'index']);
            Route::get('/min', [VehicleController::class, 'indexMin']);
            Route::get('/{id}', [VehicleController::class, 'show']);
            Route::put('/edit/{id}', [VehicleController::class, 'update']);
            Route::post('/', [VehicleController::class, 'store']);
        });

        Route::prefix('financing')->group(function () {
            Route::get('/', [FinancingController::class, 'index']);
            Route::get('/resume', [FinancingController::class, 'resume']);
            Route::get('/one/{id}', [FinancingController::class, 'getOne']);
            Route::get('/{id}', [FinancingController::class, 'show']);
            Route::get('/invoice/{id}', [FinancingController::class, 'invoice']);
            Route::put('/edit/{id}', [FinancingController::class, 'update']);
        });

        Route::prefix('apply')->group(function () {
            Route::get('/', [ApplyController::class, 'index']);
            Route::post('/doc/{id}', [ApplyController::class, 'attach']);
            Route::put('/action/{id}', [ApplyController::class, 'action']);
            Route::get('/invoice/{id}', [ApplyController::class, 'invoice']);
            Route::post('/requirements/{id}', [ApplyController::class, 'updateRequirements']);
        });

        Route::prefix('payments')->group(function () {
            Route::get('/', [PaymentsController::class, 'index']);
            Route::put('/action/{id}', [PaymentsController::class, 'action']);
            Route::get('/resume', [PaymentsController::class, 'resume']);
        });

        Route::prefix('maintenance')->group(function () {
            Route::get('/', [MaintenanceController::class, 'index']);
            Route::get('/{id}', [MaintenanceController::class, 'show']);
            Route::get('/check-type/{id}/{cedula}', [MaintenanceController::class, 'getFinancingsOrApplys']);
            Route::put('/edit/{id}', [MaintenanceController::class, 'update']);
            Route::patch('/toggle-status/{id}', [MaintenanceController::class, 'toggleStatus']);
            Route::post('/', [MaintenanceController::class, 'store']);
        });

        Route::prefix('lotes')->group(function () {
            Route::get('/', [LotesController::class, 'index']);
            Route::post('/', [LotesController::class, 'store']);
            Route::get('/check', [LotesController::class, 'get_check_lotes']);
        });

        Route::prefix('account-methods')->group(function () {
            Route::get('/', [AccountMethodController::class, 'index']);
            Route::get('/{account_method}', [AccountMethodController::class, 'show']);
            Route::post('/', [AccountMethodController::class, 'store']);
            Route::put('/{account_method}', [AccountMethodController::class, 'update']);
            Route::patch('/{account_method}/toggle-status', [AccountMethodController::class, 'toggleStatus']);
        });

        Route::prefix('services')->group(function () {
            Route::get('/', [ServiceController::class, 'index']);
        });

        Route::prefix('cobros')->group(function () {
            Route::get('/summary', [CobrosController::class, 'summary']);
            Route::get('/pending', [CobrosController::class, 'pending']);
            Route::get('/completed', [CobrosController::class, 'completed']);
            Route::post('/notify-whatsapp', [CobrosController::class, 'notifyWhatsApp']);
            Route::post('/gps/{id}', [CobrosController::class, 'toggleGPS']);
            Route::post('/moto/{id}', [CobrosController::class, 'toggleMoto']);
            Route::post('/mora', [CobrosController::class, 'storeMora']);
        });

        Route::middleware(['superadmin'])->group(function () {
            Route::delete('users/{uuid}', [UserController::class, 'destroy']);
            Route::delete('clients/{uuid}', [ClientController::class, 'destroy']);
            Route::delete('roles/{id}', [RoleController::class, 'destroy']);
            Route::delete('vehicles/{id}', [VehicleController::class, 'destroy']);
            Route::delete('financing/{id}', [FinancingController::class, 'destroy']);
            Route::delete('lotes/{id}', [LotesController::class, 'destroy']);
            Route::delete('maintenance/{id}', [MaintenanceController::class, 'destroy']);
            Route::delete('account-methods/{account_method}', [AccountMethodController::class, 'destroy']);

            // Financing Edit Routes
            Route::prefix('financing')->group(function () {
                Route::post('/requirements/{id}', [FinancingController::class, 'updateRequirements']);
                Route::post('/mora/{id}', [FinancingController::class, 'moraStatus']);
                Route::post('/', [FinancingController::class, 'store']);
                Route::put('/finance-details/{id}', [FinancingController::class, 'updateFinanceDetails']);
                Route::put('/placa/{id}', [FinancingController::class, 'updatePlaca']);
            });
        });
    });
});

Route::prefix('error')->group(function () {
    Route::get('/not-auth', function () {
        abort(403, 'This action is not authorized.');
    });

    Route::get('/not-found', function () {
        abort(404, 'Page not found.');
    });

    Route::get('/', function () {
        abort(500, 'Something went wrong');
    });

    Route::get('/custom', function () {
        throw new \App\Exceptions\CustomException('Error: Levi Strauss & CO.', 501);
    });
});