<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Tymon\JWTAuth\Facades\JWTAuth;

class AccountCenterAutoLoginController extends Controller
{
    public function autoLogin(Request $request)
    {
        $accountCenterUrl = rtrim(env('ACCOUNT_CENTER_URL', 'https://accountcenter.asipbook.com'), '/');
        $serverUrl = rtrim(env('ACCOUNT_CENTER_SERVER_URL', 'https://accountcenterserver.asipbook.com'), '/');
        $appUrl = env('APP_URL', 'https://192.168.1.18 ');

        $token = $this->getToken($request);

        if (!$token) {
            return redirect($accountCenterUrl);
        }

        $userData = $this->fetchUserData($token, $serverUrl);


        if (empty($userData)) {
            return redirect("$accountCenterUrl/api/auth/check-auth");
        }

        // Activity Log: System Login
        if (isset($userData['userData'])) {
            activity()
                ->withProperties([
                    'causer_name' => $userData['userData']['full_name'] ?? 'Unknown User',
                    'email' => $userData['userData']['Email'] ?? '',
                    'user_id' => $userData['userData']['idUser'] ?? null,
                    'ip' => request()->ip()
                ])
                ->log('login');
        }

        $this->setSessionData($userData);

        // Fetch and cache system-wide data
        $this->fetchAndCacheSystemData($token, $serverUrl);

        session(['auth_token' => $token]);
        // Optionally set cookie for further requests
        cookie()->queue('access_token', $token, 20); // 20 minutes

        session(['company_name' => $userData['userData']['company']['Company_Name']]);

        return redirect($appUrl);
    }

    private function getToken(Request $request)
    {
        return $request->input('token') ?? $request->cookie('access_token');
    }

    private function fetchUserData($token, $serverUrl)
    {
        try {
            // 1. Initial Verification (POST)
            $response = Http::timeout(60)->withHeaders([
                'Authorization' => "Bearer $token"
            ])->post("$serverUrl/api/auth/micro-finance-auth-verify");

            if ($response->ok() && $response->reason() == 'OK') {
                $data = $response->json();
                return $data;
            }
        } catch (\Exception $e) {
            // Continue to fallback
            return redirect( rtrim(env('ACCOUNT_CENTER_URL', 'https://accountcenter.asipbook.com'), '/'));
        }

        try {
            // 2. Fallback Verification (GET)
            $authResponse = Http::timeout(60)->withHeaders([
                'Authorization' => "Bearer $token"
            ])->get("$serverUrl/api/auth/check-auth");

            if ($authResponse->ok()) {
                return $authResponse->json();
            }
        } catch (\Exception $e) {
            return null;
        }

        return null;
    }

    private function setSessionData($responseData)
    {
        // Store user info
        session(['auth_user' => $responseData['user'] ?? null]);

        $userData = $responseData['userData'] ?? [];

        // Filter branches
        $filteredBranches = array_values(array_filter($userData['branches'] ?? [], function ($branch) {
            return ($branch['idBranch'] ?? null) != -1;
        }));

        $userData['branches'] = $filteredBranches;

        $userId = $userData['idUser'] ?? null;
        if ($userId) {
            session(['user_id' => $userId]); 
            Cache::put('user_data:' . $userId, $userData, now()->addMinutes(120));
        }

        session(['head_branch' => $userData['microfinanceHeadBranchId'] ?? null]);

        $hasBranchAccess = 0;
        if (isset($userData['privileges']) && is_array($userData['privileges'])) {
            foreach ($userData['privileges'] as $priv) {
                if (isset($priv['Description']) && $priv['Description'] === 'BRANCH_ACCESS') {
                    $hasBranchAccess = 1;
                    break;
                }
            }
        }
        session(['branch_access' => $hasBranchAccess]);
        if (!empty($filteredBranches)) {
            $selectedBranch = $filteredBranches[0];
            $headBranchId = $userData['microfinanceHeadBranchId'] ?? null;

            foreach ($filteredBranches as $branch) {
                if (($branch['idBranch'] ?? null) != $headBranchId) {
                    $selectedBranch = $branch;
                    break;
                }
            }

            session(['branch_id' => $selectedBranch['idBranch']]);
            if (isset($selectedBranch['Name'])) {
                session(['branch_name' => $selectedBranch['Name']]);
            }
        }

        // dd($userData);
        if (isset($userData['company']['Name'])) {
            session(['company_name' => $userData['company']['Name']]);
        }

        // Privileges moved to cache
        // if (isset($userData['privileges'])) {
        //    session(['privileges' => $userData['privileges']]);
        // }
    }

    private function fetchAndCacheSystemData($token, $serverUrl)
    {
        try {
            $response = Http::timeout(60)->withHeaders([
                'Authorization' => "Bearer $token"
            ])->get("$serverUrl/api/microfinance/users-data-for-micro-finance");

            if ($response->ok()) {
                $data = $response->json();

                // dd(isset($data['branchDetails']));
                if (isset($data['users'])) {
                    Cache::put('all_users', $data['users'], now()->addMinutes(120));
                }
                if (isset($data['branchDetails'])) {
                    Cache::put('all_branches', $data['branchDetails'], now()->addMinutes(120));
                }
            }
        } catch (\Exception $e) {
            // Silently fail or log if needed
            // \Log::error('Failed to fetch system data: ' . $e->getMessage());
        }
    }
}
