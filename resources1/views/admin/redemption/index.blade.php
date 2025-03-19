@extends('layouts.app', [
    'class' => '',
    'activeSection' => 'redemption',
])

@section('content')
    <div class="content">
        <div class="container-fluid mt--7">
            <div class="row">
                <div class="col">
                    <div class="card shadow">
                        <div class="card-header border-0">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <h3 class="mb-0">Redemption Requests</h3>
                                </div>
                                <div class="col-4 text-right">
                                    <!-- Additional actions or buttons can be added here if needed -->
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover align-items-center table-flush"
                                id='datatable'>
                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col">ID</th>
                                        <th scope="col">User</th>
                                        <th scope="col">Type</th>
                                        <th scope="col">Details</th>
                                        <th scope="col">Points</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Time</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($redeemRequests as $request)
                                        <tr>
                                            <td>{{ $request->id }}</td>
                                            <td>
                                                <div class="user-info">
                                                    <strong>
                                                        <a href="{{ route('admin.users.show', optional($request->user)->id ?? '#') }}"
                                                            class="user-link">
                                                            {{ optional($request->user)->name ?? 'Guest User' }}
                                                        </a>
                                                    </strong>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="transaction-info" style="font-size: 12px">
                                                    <span class="badge badge-info mb-2">
                                                        {{ ucfirst($request->transaction_type) }}
                                                    </span>
                                                    @if ($request->transaction_id)
                                                        <div class="transaction-detail mt-1">
                                                            <i class="fas fa-hashtag text-muted"></i>
                                                            <span class="text-black font-weight-bold">
                                                                {{ $request->transaction_id }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                    @if ($request->remark)
                                                        <div class="transaction-detail mt-1">
                                                            <i class="fas fa-comment text-muted"></i>
                                                            <span class="text-black font-italic">
                                                                {{ $request->remark }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                @if ($request->transaction_type === 'gift')
                                                    <div>
                                                        <strong>Gift:</strong>
                                                        {{ optional($request->gift)->name ?? 'No Gift' }}<br>
                                                        <a href="{{ asset('images/gifts/' . basename(optional($request->gift)->image ?? 'default.jpg')) }}"
                                                            target="_blank">
                                                            <img src="{{ asset('images/gifts/' . basename(optional($request->gift)->image ?? 'default.jpg')) }}"
                                                                alt="Gift Image" class="img-thumbnail"
                                                                style="max-width: 80px; height: auto; margin-top: 5px;">
                                                        </a>
                                                    </div>
                                                @elseif ($request->transaction_type === 'account')
                                                    <div class="compact-details">
                                                        <strong>Bank Details:</strong>
                                                        <ul class="list-unstyled mb-0">
                                                            <li><i class="fas fa-university"></i>
                                                                {{ optional($request->user)->bank_name ?? 'N/A' }}</li>
                                                            <li><i class="fas fa-credit-card"></i>
                                                                {{ optional($request->user)->account_number ?? 'N/A' }}
                                                            </li>
                                                            <li><i class="fas fa-code-branch"></i>
                                                                {{ optional($request->user)->ifsc_code ?? 'N/A' }}</li>
                                                        </ul>
                                                    </div>
                                                @elseif ($request->transaction_type === 'upi')
                                                    <div>
                                                        <strong><i class="fas fa-mobile-alt"></i> UPI:</strong>
                                                        {{ optional($request->user)->mobile_number ?? 'N/A' }}
                                                    </div>
                                                @endif
                                            </td>

                                            <td>{{ $request->points }}</td>
                                            <td>
                                                <span
                                                    class="badge badge-{{ $request->status === 'approved' ? 'success' : ($request->status === 'rejected' ? 'danger' : 'warning') }}">
                                                    {{ ucfirst($request->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                {{ $request->created_at->format('d/m/Y') }}<br>
                                                {{ $request->created_at->format('H:i') }}
                                            </td>
                                            <td style="width:150px;">
                                                @if (in_array($request->transaction_type, ['upi', 'account']))
                                                    <form action="{{ route('admin.redemption.approve', $request->id) }}"
                                                        method="POST" style="display:inline;">
                                                        @csrf
                                                        <div class="form-group">
                                                            <input type="text" name="transaction_id"
                                                                class="form-control form-control-sm"
                                                                placeholder="Transaction ID"
                                                                value="{{ $request->transaction_id ?? '' }}" required
                                                                {{ $request->status === 'approved' ? 'disabled' : '' }}>
                                                        </div>
                                                        <button type="submit" class="btn btn-success btn-sm"
                                                            title="Approve"
                                                            {{ $request->status === 'approved' ? 'disabled' : '' }}>
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('admin.redemption.approve', $request->id) }}"
                                                        method="POST" style="display:inline;">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success btn-sm"
                                                            title="Approve"
                                                            {{ $request->status === 'approved' ? 'disabled' : '' }}>
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                @endif

                                                <form action="{{ route('admin.redemption.reject', $request->id) }}"
                                                    method="POST" style="display:inline;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Reject"
                                                        {{ $request->status === 'rejected' ? 'disabled' : '' }}>
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
