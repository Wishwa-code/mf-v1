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
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        try {
            $userId = auth()->user()->id;
            $today = Carbon::today();
            $type = $request->query('type');

            $verification = DailyOdometerVerification::where('user_id', $userId)
                ->whereDate('created_at', $today)
                ->where('upload_time', $type)
                ->first();

            return response()->json([
                'uploaded' => $verification ? true : false,
                'image_url' => $verification ? asset($verification->image_url) : null
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
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            $userId = auth()->user()->id;
            $image = $request->file('image');
            $type = $request->input('type');

            // Generate a unique filename
            $filename = time() . '_' . $type . '_' . $image->getClientOriginalName();

            // Store directly in public folder to avoid symlink/URL issues
            $destinationPath = public_path('uploads/daily_odometer');

            // Ensure directory exists
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }

            $image->move($destinationPath, $filename);

            $dbPath = 'uploads/daily_odometer/' . $filename;

            $today = Carbon::today();
            $verification = DailyOdometerVerification::where('user_id', $userId)
                ->whereDate('created_at', $today)
                ->where('upload_time', $type)
                ->first();

            if ($verification) {
                // Ideally delete old image here
                $verification->image_url = $dbPath;
                $verification->save();
            } else {
                DailyOdometerVerification::create([
                    'user_id' => $userId,
                    'image_url' => $dbPath,
                    'upload_time' => $type,
                ]);
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