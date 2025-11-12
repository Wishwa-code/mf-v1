<?php

namespace App\Http\Controllers;

use App\Models\OtherChargesCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OtherChargesCodeController extends Controller
{
    public function index()
    {
        $codes = OtherChargesCode::orderBy('created_at', 'desc')->get();
        return view('pages.OtherChargesCodes', compact('codes'));
    }

    public function list()
    {
        $codes = OtherChargesCode::orderBy('created_at', 'desc')->get();
        return response()->json($codes);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:50|unique:other_charges_codes,code',
            'description' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            $code = OtherChargesCode::create([
                'code' => $request->code,
                'description' => $request->description
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Charge code created successfully',
                'data' => $code
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create charge code: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $code = OtherChargesCode::findOrFail($id);
            return response()->json([
                'status' => 'success',
                'data' => $code
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Charge code not found'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:50|unique:other_charges_codes,code,' . $id,
            'description' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            $code = OtherChargesCode::findOrFail($id);
            $code->update([
                'code' => $request->code,
                'description' => $request->description
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Charge code updated successfully',
                'data' => $code
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update charge code: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $code = OtherChargesCode::findOrFail($id);
            $code->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Charge code deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete charge code: ' . $e->getMessage()
            ], 500);
        }
    }
}
