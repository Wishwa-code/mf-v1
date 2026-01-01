<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PaymentVoucherController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        if ($response = $this->ensureSession()) {
            return $response;
        }

        $limit = (int) $request->input('limit', 50);
        if ($limit <= 0 || $limit > 100) {
            $limit = 50;
        }

        $query = tableWithBranch('payment_vouchers');

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->filled('search')) {
            $search = '%' . trim($request->input('search')) . '%';
            $query->where(function ($builder) use ($search) {
                $builder->where('voucher_no', 'like', $search)
                    ->orWhere('user_name', 'like', $search)
                    ->orWhere('branch_name', 'like', $search);
            });
        }

        $vouchers = $query
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get([
                'id',
                'voucher_no',
                'type',
                'show_date',
                'due_date',
                'branch_id',
                'branch_name',
                'user_id',
                'user_name',
                'supplier_id',
                'total_amount',
                'discount_amount',
                'tax_amount',
                'sub_total_amount',
                'status',
                'created_at',
            ]);

        return response()->json(['data' => $vouchers]);
    }

    public function show(int $id): JsonResponse
    {
        if ($response = $this->ensureSession()) {
            return $response;
        }

        $voucher = tableWithBranch('payment_vouchers')
            ->where('id', $id)
            ->first();

        if (!$voucher) {
            return response()->json(['message' => 'Payment voucher not found'], 404);
        }

        $items = DB::table('payment_voucher_items')
            ->where('payment_voucher_id', $id)
            ->orderBy('serial_no')
            ->get([
                'id',
                'serial_no',
                'description',
                'amount',
                'attachment_path',
                'created_at',
            ]);

        return response()->json([
            'data' => [
                'voucher' => $voucher,
                'items' => $items,
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        if ($response = $this->ensureSession()) {
            return $response;
        }

        $payload = $request->all();
        $itemsPayload = Arr::get($payload, 'items', []);
        if (is_string($itemsPayload)) {
            $decoded = json_decode($itemsPayload, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $payload['items'] = $decoded;
            }
        }

        $validator = Validator::make($payload, [
            'voucher_no' => 'nullable|string|max:50',
            'type' => 'required|string|in:supplier,salary,utility',
            'show_date' => 'required|date',
            'due_date' => 'nullable|date',
            'debit_account_type' => 'required|string|max:100',
            'debit_account_id' => 'nullable|integer',
            'credit_account' => 'required|string|max:100',
            'supplier_id' => 'nullable|integer',
            'discount_amount' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'status' => 'nullable|string|in:draft,submitted,approved,paid',
            'amount_in_words' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.serial_no' => 'nullable|integer|min:1',
            'items.*.description' => 'required|string',
            'items.*.amount' => 'required|numeric|min:0.01',
            'items.*.invoice_no' => 'nullable|string|max:100',
            'items.*.invoice_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();
        $items = collect($validated['items']);
        $files = $request->file('items', []);

        if ($validated['type'] === 'supplier' && empty($validated['supplier_id'])) {
            return response()->json([
                'message' => 'Supplier is required for supplier vouchers',
            ], 422);
        }

        $branchId = (int) session('branch_id');
        $voucherNo = $validated['voucher_no'] ?? $this->generateVoucherNumber($branchId);

        $exists = DB::table('payment_vouchers')->where('voucher_no', $voucherNo)->exists();
        if ($exists) {
            return response()->json([
                'message' => 'Voucher number already exists',
            ], 422);
        }

        $totalAmount = round($items->sum('amount'), 2);
        $discount = round((float) ($validated['discount_amount'] ?? 0), 2);
        $tax = round((float) ($validated['tax_amount'] ?? 0), 2);
        $subTotal = round($totalAmount - $discount + $tax, 2);

        $amountInWords = $validated['amount_in_words'] ?? $this->formatAmountInWords($subTotal);

        $branchName = session('branch_name');
        if (!$branchName) {
            $branchName = DB::table('branch')->where('branch_id', $branchId)->value('Name') ?? '';
        }

        $userId = (int)session('user_data')["idUser"];
        $userName = (string) session('username');

        $voucherId = DB::transaction(function () use (
            $validated,
            $items,
            $voucherNo,
            $totalAmount,
            $discount,
            $tax,
            $subTotal,
            $amountInWords,
            $branchName,
            $userId,
            $userName,
            $files
        ) {
            $voucherData = [
                'voucher_no' => $voucherNo,
                'type' => $validated['type'],
                'show_date' => Carbon::parse($validated['show_date'])->format('Y-m-d'),
                'due_date' => isset($validated['due_date']) ? Carbon::parse($validated['due_date'])->format('Y-m-d') : null,
                'branch_name' => $branchName,
                'user_id' => $userId,
                'user_name' => $userName,
                'debit_account_type' => $validated['debit_account_type'],
                'debit_account_id' => $validated['debit_account_id'] ?? null,
                'credit_account' => $validated['credit_account'],
                'supplier_id' => $validated['supplier_id'] ?? null,
                'total_amount' => $totalAmount,
                'discount_amount' => $discount,
                'tax_amount' => $tax,
                'sub_total_amount' => $subTotal,
                'amount_in_words' => $amountInWords,
                'status' => $validated['status'] ?? 'draft',
                'created_by' => $userId,
                'created_by_name' => $userName,
            ];

            $voucherId = insertWithBranch('payment_vouchers', $voucherData);

            foreach ($items as $index => $item) {
                $serial = $item['serial_no'] ?? ($index + 1);
                $attachmentPath = $this->storeItemAttachment(
                    Arr::get($files, "$index.files"),
                    $voucherId
                );

                DB::table('payment_voucher_items')->insert([
                    'payment_voucher_id' => $voucherId,
                    'serial_no' => $serial,
                    'description' => $item['description'],
                    'amount' => round((float) $item['amount'], 2),
                    'attachment_path' => $attachmentPath,
                ]);
            }

            return $voucherId;
        });

        return response()->json([
            'message' => 'Payment voucher created successfully',
            'data' => [
                'id' => $voucherId,
                'voucher_no' => $voucherNo,
            ],
        ], 201);
    }

    public function nextNumber(): JsonResponse
    {
        if ($response = $this->ensureSession()) {
            return $response;
        }

        $voucherNo = $this->generateVoucherNumber((int) session('branch_id'));

        return response()->json(['data' => ['voucher_no' => $voucherNo]]);
    }

    private function ensureSession(): ?JsonResponse
    {
        if (!session()->has('branch_id') || !session()->has('userid')) {
            return response()->json(['message' => 'Session expired, please login again.'], 401);
        }

        return null;
    }

    private function generateVoucherNumber(int $branchId): string
    {
        $prefix = 'PV-' . str_pad((string) $branchId, 3, '0', STR_PAD_LEFT) . '-' . Carbon::now()->format('Ym');

        $latest = DB::table('payment_vouchers')
            ->where('voucher_no', 'like', $prefix . '%')
            ->orderByDesc('voucher_no')
            ->value('voucher_no');

        $sequence = 1;
        if ($latest) {
            $parts = explode('-', $latest);
            $lastPart = end($parts);
            if (is_numeric($lastPart)) {
                $sequence = (int) $lastPart + 1;
            }
        }

        return $prefix . '-' . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }

    private function formatAmountInWords(float $amount): string
    {
        if ($amount == 0.0) {
            return 'Zero';
        }

        $sign = $amount < 0 ? 'Minus ' : '';
        $amount = abs($amount);

        $intPart = (int) floor($amount);
        $decPart = (int) round(($amount - $intPart) * 100);

        $words = $this->convertNumberToWords($intPart);
        $words = $words !== '' ? $words : 'Zero';

        if ($decPart > 0) {
            $words .= ' and ' . str_pad((string) $decPart, 2, '0', STR_PAD_LEFT) . '/100';
        }

        return $sign . $words;
    }

    private function convertNumberToWords(int $number): string
    {
        if ($number === 0) {
            return '';
        }

        $ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine'];
        $teens = ['Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
        $tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];

        $result = '';

        if ($number >= 1000000) {
            $result .= $this->convertNumberToWords((int) floor($number / 1000000)) . ' Million ';
            $number %= 1000000;
        }

        if ($number >= 1000) {
            $result .= $this->convertNumberToWords((int) floor($number / 1000)) . ' Thousand ';
            $number %= 1000;
        }

        if ($number >= 100) {
            $result .= $ones[(int) floor($number / 100)] . ' Hundred ';
            $number %= 100;
        }

        if ($number >= 20) {
            $result .= $tens[(int) floor($number / 10)];
            if ($number % 10) {
                $result .= ' ' . $ones[$number % 10];
            }
        } elseif ($number >= 10) {
            $result .= $teens[$number - 10];
        } elseif ($number > 0) {
            $result .= $ones[$number];
        }

        return trim($result);
    }

    private function storeItemAttachment($files, int $voucherId): ?string
    {
        if ($files instanceof UploadedFile) {
            $files = [$files];
        }

        if (!is_array($files) || empty($files)) {
            return null;
        }

        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $path = $file->store('uploads/payment_vouchers/' . $voucherId, 'public');
                if ($path) {
                    return '/storage/' . ltrim($path, '/');
                }
            }
        }

        return null;
    }
}
