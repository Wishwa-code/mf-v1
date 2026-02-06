<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\DailyOdometerVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class DailyVerificationController extends Controller
{
    public function checkOdometerImageStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:day_start,day_end',
            'user_id' => 'required', // User said they will send user id
            'date' => 'sometimes|date', // Optional, defaults to today
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        try {
            $userId = $request->input('user_id');
            $date = $request->input('date', Carbon::today()->toDateString());
            $type = $request->input('type');

            $verification = DailyOdometerVerification::where('collector_id', $userId)
                ->whereDate('date', $date)
                ->first();

            $uploaded = false;
            $imageUrl = null;

            if ($verification) {
                if ($type === 'day_start' && $verification->start_photo) {
                    $uploaded = true;
                    $imageUrl = asset($verification->start_photo);
                } elseif ($type === 'day_end' && $verification->end_photo) {
                    $uploaded = true;
                    $imageUrl = asset($verification->end_photo);
                }
            }

            return response()->json([
                'uploaded' => $uploaded,
                'image_url' => $imageUrl
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Internal Server Error', 'message' => $e->getMessage()], 500);
        }
    }

    public function uploadOdometerImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max
            'type' => 'required|in:day_start,day_end',
            'user_id' => 'required',
            'user_name' => 'required|string',
            'location' => 'nullable|string',
            'reading_value' => 'required|numeric',
            'date' => 'sometimes|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            $userId = $request->input('user_id');
            $userName = $request->input('user_name');
            $location = $request->input('location');
            $readingValue = $request->input('reading_value');
            $type = $request->input('type');
            $date = $request->input('date', Carbon::today()->toDateString());

            $image = $request->file('image');

            // Generate a unique filename
            $filename = time() . '_' . $type . '_' . $userId . '_' . $image->getClientOriginalName();

            // Store directly in public folder
            $destinationPath = public_path('uploads/daily_odometer');

            // Ensure directory exists
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }

            $image->move($destinationPath, $filename);
            $dbPath = 'uploads/daily_odometer/' . $filename;

            // Find existing record for this user and date
            $verification = DailyOdometerVerification::where('collector_id', $userId)
                ->where('date', $date)
                ->first();

            if ($type === 'day_start') {
                if ($verification) {
                    // Update existing
                    $verification->update([
                        'start_photo' => $dbPath,
                        'start_reading_value' => $readingValue,
                        'start_location' => $location,
                        'start_upload_time' => Carbon::now()->toTimeString(),
                        'collector_name' => $userName, // Update name just in case
                    ]);
                } else {
                    // Create new
                    DailyOdometerVerification::create([
                        'collector_id' => $userId,
                        'collector_name' => $userName,
                        'date' => $date,
                        'start_photo' => $dbPath,
                        'start_reading_value' => $readingValue,
                        'start_location' => $location,
                        'start_upload_time' => Carbon::now()->toTimeString(),
                    ]);
                }
            } else { // day_end
                if ($verification) {
                    $verification->update([
                        'end_photo' => $dbPath,
                        'end_reading_value' => $readingValue,
                        'end_location' => $location,
                        'end_upload_time' => Carbon::now()->toTimeString(),
                    ]);
                } else {
                    // Create new if end is uploaded first (unlikely but possible logic)
                    DailyOdometerVerification::create([
                        'collector_id' => $userId,
                        'collector_name' => $userName,
                        'date' => $date,
                        'end_photo' => $dbPath,
                        'end_reading_value' => $readingValue,
                        'end_location' => $location,
                        'end_upload_time' => Carbon::now()->toTimeString(),
                        // Start fields will be nullable/empty
                        'start_reading_value' => 0, // Fallback or nullable? Schema says NOT NULL for start_reading_value.
                        // User SQL says: odometer_value -> start_reading_value DOUBLE NOT NULL.
                        // So we must provide a value if creating new. I'll use 0 or readingValue if logically it's the only reading.
                        // But strictly speaking, if day_end is first, start is missing.
                        // I will set start_reading_value to 0 to avoid SQL error, or make it nullable in migration?
                        // User SQL: CHANGE COLUMN ... start_reading_value DOUBLE ... NOT NULL.
                        // So I must provide it. I'll use 0.
                        'start_photo' => '', // Schema says NOT NULL.
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Image uploaded successfully',
                'uploaded' => true,
                'image_url' => asset($dbPath)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Upload failed', 'message' => $e->getMessage()], 500);
        }
    }
}