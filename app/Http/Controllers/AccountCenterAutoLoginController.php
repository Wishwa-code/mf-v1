<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Tymon\JWTAuth\Facades\JWTAuth;

class AccountCenterAutoLoginController extends Controller
{
    public function autoLogin(Request $request)
    {
        // Get token from cookie or POST
        $token = $request->input('token') ?? $request->cookie('access_token');
        // dd($token);

        if (!$token) {
            return redirect('https://accountcenter.asipbook.com/');
        }

        // Call MERN backend to verify JWT
        $response = Http::withHeaders([
            'Authorization' => "Bearer $token"
        ])->post('https://accountcenterserver.asipbook.com/api/auth/micro-finance-auth-verify');

        // dd($response, !$response->ok() || !$response['valid']);
        if (!$response->ok() || !$response['valid']) {
            $userData = Http::withHeaders([
                'Authorization' => "Bearer $token"
            ])->get('https://accountcenterserver.asipbook.com/api/auth/check-auth');

            return redirect('https://accountcenter.asipbook.com/api/auth/check-auth');
        }

        // Store user info in Laravel session
        session(['auth_user' => $response['user']]);

        // Filter branches where idBranch != -1
        $filteredBranches = array_values(array_filter($response['userData']['branches'] ?? []));

        $userData = $response['userData'];
        $userData['branches'] = $filteredBranches;

        session(['user_data' => $userData]);

        if (!empty($filteredBranches)) {
            session(['branch_id' => $filteredBranches[0]['idBranch']]);
            if (isset($filteredBranches[0]['Name'])) {
                session(['branch_name' => $filteredBranches[0]['Name']]);
            }
        }

        if (isset($response['userData']['privileges'])) {
            session(['privileges' => $response['userData']['privileges']]);
        }

        // Optionally set cookie for further requests
        cookie()->queue('access_token', $token, 20); // 20 minutes
        // dd($response['userData']['privileges']   );
        return redirect('https://192.168.1.18/'); // dashboard route
    }
}
