{{-- resources/views/pages/partials/customer_pagination.blade.php --}}
<div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
    <div>
        Showing
        <strong>{{ $customers->firstItem() ?? 0 }}</strong>
        to
        <strong>{{ $customers->lastItem() ?? 0 }}</strong>
        of
        <strong>{{ $customers->total() }}</strong>
        customers
    </div>
    <div>
        {{ $customers->appends(request()->query())->links('pagination::bootstrap-5') }}
    </div>
</div>
