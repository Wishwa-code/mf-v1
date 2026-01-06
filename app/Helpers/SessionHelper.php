<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

if (!function_exists('user_data')) {
    /**
     * Get user data from Cache (backed by Session ID)
     *
     * @param string|null $key Optional key to retrieve specific attribute
     * @return mixed
     */
    function user_data($key = null)
    {
        $userId = Session::get('user_id');

        if (!$userId) {
            return null;
        }

        // Retrieve full user data from cache
        $userData = Cache::get('user_data:' . $userId);

        if (!$userData) {
            // Fallback: Check if it's still in session (during migration) or return null
            // For now, we assume strict cache usage as per plan.
            // You might want to remove this fallback later.
            $userData = Session::get('user_data');
            if ($userData) {
                // Auto-migrate to cache if found? 
                // Cache::put('user_data:' . $userId, $userData, 120 * 60);
                return $key ? ($userData[$key] ?? null) : $userData;
            }
            return null;
        }

        if ($key) {
            return $userData[$key] ?? null;
        }

        return $userData;
    }
}
