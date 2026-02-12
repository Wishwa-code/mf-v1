<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\UserLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class UserLocationController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:user,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'accuracy' => 'nullable|numeric',
            'speed' => 'nullable|numeric',
            'heading' => 'nullable|numeric',
            'captured_at' => 'nullable|date',
            'device_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        try {
            $capturedAt = $request->input('captured_at')
                ? Carbon::parse($request->input('captured_at'))
                : Carbon::now();

            $location = UserLocation::create([
                'user_id' => $request->input('user_id'),
                'latitude' => $request->input('latitude'),
                'longitude' => $request->input('longitude'),
                'accuracy' => $request->input('accuracy'),
                'speed' => $request->input('speed'),
                'heading' => $request->input('heading'),
                'captured_at' => $capturedAt,
                'device_id' => $request->input('device_id'),
            ]);

            return response()->json([
                'message' => 'Location stored successfully',
                'data' => $location
            ], 201);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Internal Server Error', 'message' => $e->getMessage()], 500);
        }
    }
}
