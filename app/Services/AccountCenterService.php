<?php

namespace App\Services;

use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class AccountCenterService
{
    protected $client;
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.account_center.url');

        $this->client = new Client([
            'timeout'  => 10,
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
            ],
        ]);
    }

    /**
     * Sync user details to Account Center
     *
     * @param User $user
     * @return array|null
     */
    public function syncUser(User $user)
    {
        if (empty($this->baseUrl)) {
            Log::warning('[AccountCenter] URL not configured. Skipping sync for User ID: ' . $user->id);
            return null;
        }

        try {
            $payload = [
                'external_id' => $user->id,
                'email'       => $user->email,
                'full_name'   => $user->Full_Name,
                'designation' => $user->Designation,
                'epf_no'      => $user->Epf_no,
                'nic'         => $user->Nic,
                'tp'          => $user->TP,
                'branch_id'   => $user->branch_id,
                'status'      => $user->Status,
            ];

            $response = $this->client->post($this->baseUrl, [
                'json' => $payload
            ]);

            $responseData = json_decode($response->getBody()->getContents(), true);

            Log::info('[AccountCenter] User synced successfully.', [
                'user_id' => $user->id,
                'response' => $responseData
            ]);

            return $responseData;
        } catch (\Exception $e) {
            Log::error('[AccountCenter] Failed to sync user.', [
                'user_id' => $user->id,
                'error'   => $e->getMessage()
            ]);

            // We return null or throw depending on if we want to block the approval flow.
            // For now, let's log and continue so we don't break the approval flow.
            return null;
        }
    }
}
