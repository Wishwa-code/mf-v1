<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\{AuthController, DirectoryController, CollectionsController, LoansController,CustomersController,PaymentsController};
use App\Http\Middleware\ApplyBranchFromUser;

Route::post('/login', [AuthController::class, 'apiLogin']);

Route::middleware(['auth:sanctum', ApplyBranchFromUser::class])->group(function () {
    // existing
    Route::get('/routes', [DirectoryController::class, 'listRoutes']);
    Route::get('/centers', [DirectoryController::class, 'listCenters']);
    Route::get('/groups',  [DirectoryController::class, 'listGroups']);
    Route::get('/centers/{center_id}/groups', [DirectoryController::class, 'groupsByCenter'])->whereNumber('center_id');
    Route::get('/collections',       [CollectionsController::class, 'index']);
    Route::get('/collections/today', [CollectionsController::class, 'today']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // --- Loans (specific first) ---
    Route::get('/loans/search', [LoansController::class, 'byCustomer']);
    Route::get('/loans/{id}/payments', [LoansController::class, 'payments'])->whereNumber('id');
    Route::get('/loans', [LoansController::class, 'index']);   // list with filters + totals
    Route::get('/loans/{id}', [LoansController::class, 'show'])->whereNumber('id');

    // Customers
    Route::get('/customers', [CustomersController::class, 'index']);
    Route::get('/customers/{id}', [CustomersController::class, 'show'])->whereNumber('id');
    Route::post('/loans/{id}/payments', [PaymentsController::class, 'storeLoanPayment'])->whereNumber('id');
});

