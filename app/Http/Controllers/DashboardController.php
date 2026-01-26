<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BankLogController;
use App\Http\Controllers\CapitalBalanceController;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;

class DashboardController extends Controller
{
    public function index(Store $session)
    {
        return view('home');
    }
}
