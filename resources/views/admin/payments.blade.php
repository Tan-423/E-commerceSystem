@extends('layouts.admin')
@section('content')

<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Payment Records</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li>
                    <a href="{{route('admin.index')}}">
                        <div class="text-tiny">Dashboard</div>
                    </a>
                </li>
                <li>
                    <i class="icon-chevron-right"></i>
                </li>
                <li>
                    <div class="text-tiny">Payment Records</div>
                </li>
            </ul>
        </div>

        <div class="wg-box">
            <div class="flex items-center justify-between gap10 flex-wrap">
                <div class="flex gap10">
                    <form method="GET" class="form-filter">
                        <select name="status" class="form-select" onchange="this.form.submit()">
                            <option value="">All Status</option>
                            @foreach($statuses as $status)
                                <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                    {{ ucfirst($status) }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                    @if(request('status'))
                        <a href="{{ route('admin.payments') }}" class="btn btn-outline-secondary">
                            <i class="icon-refresh"></i> Clear Filters
                        </a>
                    @endif
                </div>
            </div>
            
            <div class="wg-table table-all-user">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">Order Number</th>
                                <th class="text-center">Customer</th>
                                <th class="text-center">Email</th>
                                <th class="text-center">Amount</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Transaction Date</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($transactions as $transaction)
                            <tr>
                                <td class="text-center">
                                    @if($transaction->order)
                                        {{ $transaction->order->global_order_number }}
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($transaction->order)
                                        {{ $transaction->order->name }}
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($transaction->order && $transaction->order->user)
                                        {{ $transaction->order->user->email }}
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($transaction->order)
                                        ${{ number_format($transaction->order->total, 2) }}
                                    @elseif($transaction->amount)
                                        ${{ number_format($transaction->amount, 2) }}
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge 
                                        @if($transaction->status == 'approved') bg-success
                                        @elseif($transaction->status == 'pending') bg-warning
                                        @elseif($transaction->status == 'declined') bg-danger
                                        @elseif($transaction->status == 'refunded') bg-info
                                        @else bg-secondary
                                        @endif">
                                        {{ ucfirst($transaction->status) }}
                                    </span>
                                </td>
                                <td class="text-center">{{ $transaction->created_at->format('M d, Y H:i') }}</td>
                                <td class="text-center">
                                    @if($transaction->order)
                                        <a href="{{ route('admin.order_details', $transaction->order->id) }}" 
                                           class="btn btn-sm btn-outline-primary" title="View Order Details">
                                            <i class="icon-eye"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>


                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="icon-info-circle"></i>
                                        No payment records found.
                                        @if(request('status'))
                                            <br><a href="{{ route('admin.payments') }}">Clear filters</a> to see all records.
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="divider"></div>
            <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">
                {{ $transactions->appends(['status' => request('status')])->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

@endsection

