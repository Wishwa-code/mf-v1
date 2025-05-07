<?php

namespace App\Http\Controllers\API;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use GuzzleHttp\Client;

class AuthController
{
    public function apiLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['status' => 'error', 'message' => 'Invalid credentials'], 401);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || $user->Status === "0") {
            return response()->json(['status' => 'error', 'message' => 'Account is inactive'], 403);
        }

        // ✅ Generate Sanctum Token
        $token = $user->createToken('API Token')->plainTextToken;

        // Optional: Call SMS API
        $smsToken = null;
        try {
            $client = new Client(['base_uri' => 'https://e-sms.dialog.lk/api/v1/']);
            $response = $client->post('login', [
                'headers' => ['Content-Type' => 'application/json'],
                'json' => ['username' => 'ASIPIYA', 'password' => 'Dialog@123'],
            ]);

            $responseData = json_decode($response->getBody()->getContents(), true);
            $smsToken = $responseData['token'] ?? null;
        } catch (\Exception $e) {
            Log::error('SMS API Login Error: ' . $e->getMessage());
        }

        $company = DB::table('company')->first();
        $branch = DB::table('branch')->where('branch_id', $user->branch_id)->first();

        return response()->json([
            'status' => 'success',
            'message' => 'Login successful',
            'user' => [
                'id' => $user->id,
                'name' => $user->Full_Name,
                'designation' => $user->Designation,
                'branch_id' => $user->branch_id,
                'branch_name' => $branch->Name ?? '',
                'company_name' => $company->company_name ?? '',
            ],
            'auth_token' => $token,
            'sms_token' => $smsToken
        ]);
    }
}
