<?php

use App\Http\Controllers\API\DailyVerificationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\{AuthController, DirectoryController, CollectionsController, LoansController, CustomersController, PaymentsController, RoutesController, AccountLoginApiController};
use App\Http\Middleware\ApplyBranchFromUser;

Route::post('/login', [AuthController::class, 'apiLogin']);

Route::middleware(['auth:sanctum', ApplyBranchFromUser::class])->group(function () {
    // existing
    Route::get('/routes', [DirectoryController::class, 'listRoutes']);
    Route::get('/centers', [DirectoryController::class, 'listCenters']);
    Route::get('/groups', [DirectoryController::class, 'listGroups']);
    Route::get('/centers/{center_id}/groups', [DirectoryController::class, 'groupsByCenter'])->whereNumber('center_id');
    Route::get('/collections', [CollectionsController::class, 'index']);
    Route::get('/collections/today', [CollectionsController::class, 'today']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/login-details', [AccountLoginApiController::class, 'loginDetails']);

    // --- Loans (specific first) ---
    Route::get('/loans/search', [LoansController::class, 'byCustomer']);
    Route::get('/loans/{id}/payments', [LoansController::class, 'payments'])->whereNumber('id');
    Route::get('/loans', [LoansController::class, 'index']);   // list with filters + totals
    Route::get('/loans/{id}', [LoansController::class, 'show'])->whereNumber('id');

    // Customers
    Route::get('/customers', [CustomersController::class, 'index']);
    Route::get('/customers/{id}', [CustomersController::class, 'show'])->whereNumber('id');
    Route::post('/loans/{id}/payments', [PaymentsController::class, 'storeLoanPayment'])->whereNumber('id');

    Route::get('/routes', [RoutesController::class, 'index']);
    Route::get('/routes/{id}/customers', [RoutesController::class, 'customers'])->whereNumber('id');
    Route::get('/payments/{id}/receipt', [PaymentsController::class, 'paymentReceipt'])
        ->whereNumber('id');
    Route::get('/payments/my', [PaymentsController::class, 'myPayments']); // logged user payments
    Route::get('/payments/my/summary', [PaymentsController::class, 'myPaymentsSummary']); // optional totals

    Route::post('/loans/comments/store', [LoansController::class, 'storeComment']);
    Route::post('/loans/comments/fetch', [LoansController::class, 'fetchComments']);

    // Daily Verification
    Route::get('/daily-verification/status', [DailyVerificationController::class, 'checkOdometerImageStatus']);
    Route::post('/daily-verification/upload', [DailyVerificationController::class, 'uploadOdometerImage']);

    // User Location
    Route::post('/user-locations', [\App\Http\Controllers\API\UserLocationController::class, 'store']);
});
