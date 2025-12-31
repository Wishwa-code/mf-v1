<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Tymon\JWTAuth\Facades\JWTAuth;

class AccountCenterAutoLoginController extends Controller
{
    public function autoLogin(Request $request)
    {
        $accountCenterUrl = rtrim(env('ACCOUNT_CENTER_URL', 'https://accountcenter.asipbook.com'), '/');
        $serverUrl = rtrim(env('ACCOUNT_CENTER_SERVER_URL', 'https://accountcenterserver.asipbook.com'), '/');
        $appUrl = env('APP_URL', 'https://192.168.1.18');

        $token = $this->getToken($request);

        if (!$token) {
            return redirect($accountCenterUrl);
        }

        $userData = $this->fetchUserData($token, $serverUrl);

        if (empty($userData)) {
            return redirect("$accountCenterUrl/api/auth/check-auth");
        }

        $this->setSessionData($userData);

        // Optionally set cookie for further requests
        cookie()->queue('access_token', $token, 20); // 20 minutes

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
                if (!empty($data)) {
                    return $data;
                }
            }
        } catch (\Exception $e) {
            // Continue to fallback
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
        session(['user_data' => $userData]);
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
            session(['branch_id' => $filteredBranches[0]['idBranch']]);
            if (isset($filteredBranches[0]['Name'])) {
                session(['branch_name' => $filteredBranches[0]['Name']]);
            }
        }

        if (isset($userData['privileges'])) {
            session(['privileges' => $userData['privileges']]);
        }
    }
}
