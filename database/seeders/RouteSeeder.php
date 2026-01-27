<?php

namespace Database\Seeders;

use App\Models\Route;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;


class RouteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Try to get user_id from Session (if running from web)
        // 1. Try to get user_id from Session (Web Context)
        $userId = session('user_id');
        $branchId = session('branch_id');

        // 2. Fallback: Try to get from Cache (CLI Context)
        if (!$userId) {
            $allUsers = Cache::get('all_users'); // Set by AccountCenterAutoLoginController

            if ($allUsers && is_array($allUsers) && count($allUsers) > 0) {
                // Get the first user from the cached list
                $firstUser = $allUsers[0];
                // Handle different potential keys (API response vs DB structure)
                $userId = $firstUser['idUser'] ?? $firstUser['id'] ?? null;

                if ($userId) {
                    // Try to get their branch from their cached user_data
                    $userData = Cache::get('user_data:' . $userId);
                    if ($userData && !empty($userData['branches'])) {
                        $firstBranch = $userData['branches'][0] ?? null;
                        $branchId = $firstBranch['idBranch'] ?? 1;
                    }
                }
            }
        }

        // 3. Final Fallback if Cache is empty/expired
        if (!$userId) $userId = 1;
        if (!$branchId) $branchId = 1;

        // dd($userId, $branchId); // Debugging removed

        $routes = [
            [
                'route_name' => 'Colombo Central',
                'root_code' => 'COL-001',
                'id_officer' => $userId,
                'branch_id' => $branchId,
                'collection_type' => 'daily',
                'collection_date' => 'Monday',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'route_name' => 'Kandy City',
                'root_code' => 'KAN-002',
                'id_officer' => $userId,
                'branch_id' => $branchId,
                'collection_type' => 'weekly',
                'collection_date' => 'Wednesday',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'route_name' => 'Galle Fort',
                'root_code' => 'GAL-003',
                'id_officer' => $userId,
                'branch_id' => $branchId,
                'collection_type' => 'daily',
                'collection_date' => 'Friday',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'route_name' => 'Negombo Beach',
                'root_code' => 'NEG-004',
                'id_officer' => $userId,
                'branch_id' => $branchId,
                'collection_type' => 'monthly',
                'collection_date' => 'Last Friday',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'route_name' => 'Jaffna Town',
                'root_code' => 'JAF-005',
                'id_officer' => $userId,
                'branch_id' => $branchId,
                'collection_type' => 'daily',
                'collection_date' => 'Tuesday',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        Route::insert($routes);
    }
}
