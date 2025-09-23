<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\{AuthController, DirectoryController, CollectionsController, LoansController,CustomersController};
use App\Http\Middleware\ApplyBranchFromUser;

Route::post('/login', [AuthController::class, 'apiLogin']);

Route::middleware(['auth:sanctum', ApplyBranchFromUser::class])->group(function () {
    // existing
    Route::get('/routes', [DirectoryController::class, 'listRoutes']);
    Route::get('/centers', [DirectoryController::class, 'listCenters']);
    Route::get('/groups',  [DirectoryController::class, 'listGroups']);
    Route::get('/centers/{center_id}/groups', [DirectoryController::class, 'groupsByCenter']);
    Route::get('/collections',       [CollectionsController::class, 'index']);
    Route::get('/collections/today', [CollectionsController::class, 'today']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // NEW: Loans
    Route::get('/loans', [LoansController::class, 'index']);   // list with filters + totals
    Route::get('/loans/{id}', [LoansController::class, 'show']);

    // Customers
    Route::get('/customers', [CustomersController::class, 'index']);
    Route::get('/customers/{id}', [CustomersController::class, 'show']);

// Loan payments
    Route::get('/loans/{id}/payments', [LoansController::class, 'payments']);
});
