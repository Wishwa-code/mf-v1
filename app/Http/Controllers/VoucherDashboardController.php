<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VoucherDashboardController extends Controller
{
    public function index(Request $request)
    {
        if (!session()->has('branch_id')) {
            return redirect()->route('login');
        }

        $branchId = (int) session('branch_id');
        $now = Carbon::now();
        $today = Carbon::today()->toDateString();

        $filters = [
            'date_from' => $request->input('date_from'),
            'date_to' => $request->input('date_to'),
            'status' => $request->filled('status') ? $request->input('status') : null,
            'supplier_id' => $request->input('supplier_id'),
        ];

        $statusFilter = $filters['status'] ?: null;
        $hasFilters = collect($filters)
            ->filter(fn ($value) => !is_null($value) && $value !== '')
            ->isNotEmpty();

        $globalVoucherQuery = function () use ($branchId) {
            return DB::table('payment_vouchers')
                ->where('payment_vouchers.branch_id', $branchId);
        };

        $startOfMonth = $now->copy()->startOfMonth()->toDateString();
        $endOfMonth = $now->copy()->endOfMonth()->toDateString();

        $currentMonthTotal = ($globalVoucherQuery)()
            ->whereBetween('payment_vouchers.show_date', [$startOfMonth, $endOfMonth])
            ->count();

        $todayTotal = ($globalVoucherQuery)()
            ->whereDate('payment_vouchers.show_date', $today)
            ->count();

        $expiredCount = ($globalVoucherQuery)()
            ->whereNotNull('payment_vouchers.due_date')
            ->whereDate('payment_vouchers.due_date', '<', $today)
            ->whereIn('payment_vouchers.status', ['draft', 'submitted', 'approved'])
            ->count();

        $pendingApprovalCount = ($globalVoucherQuery)()
            ->where('payment_vouchers.status', 'submitted')
            ->count();

        $approvedCount = ($globalVoucherQuery)()
            ->where('payment_vouchers.status', 'approved')
            ->count();

        $currentMonthPaidAggregate = ($globalVoucherQuery)()
            ->where('payment_vouchers.status', 'paid')
            ->whereBetween('payment_vouchers.show_date', [$startOfMonth, $endOfMonth])
            ->selectRaw('COALESCE(SUM(payment_vouchers.total_amount), 0) as amount, COUNT(*) as cnt')
            ->first();

        $stats = [
            'current_month_total' => $currentMonthTotal,
            'today_total' => $todayTotal,
            'expired_total' => $expiredCount,
            'pending_total' => $pendingApprovalCount,
            'approved_total' => $approvedCount,
            'current_month_paid_amount' => (float) ($currentMonthPaidAggregate->amount ?? 0),
            'current_month_paid_count' => (int) ($currentMonthPaidAggregate->cnt ?? 0),
        ];

        $statusMeta = [
            'draft' => ['label' => 'Draft', 'class' => 'status-pending'],
            'submitted' => ['label' => 'Pending', 'class' => 'status-pending'],
            'approved' => ['label' => 'Approved', 'class' => 'status-approved'],
            'rejected' => ['label' => 'Rejected', 'class' => 'status-rejected'],
            'paid' => ['label' => 'Paid', 'class' => 'status-paid'],
        ];

        $pendingApprovals = ($globalVoucherQuery)()
            ->where('payment_vouchers.status', 'submitted')
            ->leftJoin('suppliers', 'suppliers.id', '=', 'payment_vouchers.supplier_id')
            ->orderByDesc('payment_vouchers.created_at')
            ->limit(15)
            ->get([
                'payment_vouchers.id',
                'payment_vouchers.voucher_no',
                'payment_vouchers.show_date',
                'payment_vouchers.due_date',
                'payment_vouchers.total_amount',
                'payment_vouchers.user_name',
                'payment_vouchers.credit_account',
                'suppliers.company_name',
                'suppliers.supplier_no',
            ])->map(function ($row) {
                $row->supplier_label = $row->company_name ?: '—';
                $row->payment_account_label = $this->formatPaymentAccount($row->credit_account);
                return $row;
            });

        $pendingPayments = ($globalVoucherQuery)()
            ->where('payment_vouchers.status', 'approved')
            ->leftJoin('suppliers', 'suppliers.id', '=', 'payment_vouchers.supplier_id')
            ->orderByDesc('payment_vouchers.updated_at')
            ->limit(15)
            ->get([
                'payment_vouchers.id',
                'payment_vouchers.voucher_no',
                'payment_vouchers.show_date',
                'payment_vouchers.due_date',
                'payment_vouchers.total_amount',
                'payment_vouchers.user_name',
                'payment_vouchers.credit_account',
                'payment_vouchers.updated_by_name',
                'payment_vouchers.updated_at',
                'suppliers.company_name',
                'suppliers.supplier_no',
            ])->map(function ($row) {
                $row->supplier_label = $row->company_name ?: '—';
                $row->payment_account_label = $this->formatPaymentAccount($row->credit_account);
                $row->approval_label = ($row->updated_by_name && $row->updated_at)
                    ? trim($row->updated_by_name . ' - ' . Carbon::parse($row->updated_at)->toDateString())
                    : '—';
                return $row;
            });

        $recentVouchers = ($globalVoucherQuery)()
            ->leftJoin('suppliers', 'suppliers.id', '=', 'payment_vouchers.supplier_id')
            ->orderByDesc('payment_vouchers.show_date')
            ->orderByDesc('payment_vouchers.id')
            ->limit(25)
            ->get([
                'payment_vouchers.id',
                'payment_vouchers.voucher_no',
                'payment_vouchers.show_date',
                'payment_vouchers.total_amount',
                'payment_vouchers.status',
                'payment_vouchers.credit_account',
                DB::raw('(SELECT pvi.description FROM payment_voucher_items as pvi WHERE pvi.payment_voucher_id = payment_vouchers.id ORDER BY pvi.serial_no ASC LIMIT 1) as primary_description'),
                'suppliers.company_name',
                'suppliers.supplier_no',
            ])->map(function ($row) use ($statusMeta) {
                $row->supplier_label = $row->company_name ?: '—';
                $row->description_label = $row->primary_description ?: '—';
                $row->status_label = $statusMeta[$row->status]['label'] ?? ucfirst((string) $row->status);
                $row->status_class = $statusMeta[$row->status]['class'] ?? 'status-pending';
                return $row;
            });

        $filteredVouchers = collect();
        if ($hasFilters) {
            $filteredVouchers = DB::table('payment_vouchers')
                ->where('payment_vouchers.branch_id', $branchId)
                ->when($filters['date_from'], fn ($query, $value) => $query->whereDate('payment_vouchers.show_date', '>=', $value))
                ->when($filters['date_to'], fn ($query, $value) => $query->whereDate('payment_vouchers.show_date', '<=', $value))
                ->when($filters['supplier_id'], fn ($query, $value) => $query->where('payment_vouchers.supplier_id', $value))
                ->when($statusFilter, fn ($query, $value) => $query->where('payment_vouchers.status', $value))
                ->leftJoin('suppliers', 'suppliers.id', '=', 'payment_vouchers.supplier_id')
                ->orderByDesc('payment_vouchers.show_date')
                ->orderByDesc('payment_vouchers.id')
                ->limit(100)
                ->get([
                    'payment_vouchers.id',
                    'payment_vouchers.voucher_no',
                    'payment_vouchers.show_date',
                    'payment_vouchers.due_date',
                    'payment_vouchers.total_amount',
                    'payment_vouchers.status',
                    'payment_vouchers.credit_account',
                    'payment_vouchers.user_name',
                    DB::raw('(SELECT pvi.description FROM payment_voucher_items as pvi WHERE pvi.payment_voucher_id = payment_vouchers.id ORDER BY pvi.serial_no ASC LIMIT 1) as primary_description'),
                    'suppliers.company_name',
                    'suppliers.supplier_no',
                ])->map(function ($row) use ($statusMeta) {
                    $row->supplier_label = $row->company_name ?: '—';
                    $row->description_label = $row->primary_description ?: '—';
                    $row->status_label = $statusMeta[$row->status]['label'] ?? ucfirst((string) $row->status);
                    $row->status_class = $statusMeta[$row->status]['class'] ?? 'status-pending';
                    $row->payment_account_label = $this->formatPaymentAccount($row->credit_account);
                    return $row;
                });
        }

        $suppliers = DB::table('suppliers')
            ->where('branch_id', $branchId)
            ->where('status', 1)
            ->orderBy('company_name')
            ->get(['id', 'supplier_no', 'company_name']);

        $statusOptions = [
            '' => 'All Status',
            'draft' => 'Draft',
            'submitted' => 'Pending',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'paid' => 'Paid',
        ];

        return view('pages.VoucherDashboard', [
            'stats' => $stats,
            'filters' => $filters,
            'statusOptions' => $statusOptions,
            'pendingApprovals' => $pendingApprovals,
            'pendingPayments' => $pendingPayments,
            'recentVouchers' => $recentVouchers,
            'suppliers' => $suppliers,
            'filteredVouchers' => $filteredVouchers,
            'hasFilters' => $hasFilters,
        ]);
    }

    private function formatPaymentAccount(?string $value): string
    {
        if (!$value) {
            return '—';
        }

        $clean = str_replace(['_', '-'], ' ', strtolower($value));
        return ucwords($clean);
    }
}
