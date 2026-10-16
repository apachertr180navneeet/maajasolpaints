@extends('layouts.app', [
    'class' => '',
    'activeSection' => 'redemption',
])

@section('content')
    <div class="content">
        <div class="container-fluid mt--7">
            <div class="row">
                <div class="col">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <div class="card shadow">
                        <div class="card-header border-0">
                            <div class="row align-items-center">
                                <div class="col-lg-4 col-md-12 mb-2 mb-lg-0">
                                    <h3 class="mb-0">{{ $title ?? 'Redemption Requests' }}</h3>
                                </div>
                                <div class="col-lg-8 col-md-12 text-lg-right">
                                    <a href="{{ route('admin.redemption', ['status' => 'pending']) }}"
                                       class="btn btn-sm btn-warning mb-1 {{ ($status ?? '') == 'pending' ? 'active font-weight-bold shadow-sm' : '' }}">
                                        <i class="fas fa-clock mr-1"></i> Pending Requests
                                        @if (isset($counts['pending']) && $counts['pending'] > 0)
                                            <span class="badge badge-danger ml-1">{{ $counts['pending'] }}</span>
                                        @endif
                                    </a>
                                    <a href="{{ route('admin.redemption', ['status' => 'all']) }}"
                                       class="btn btn-sm btn-secondary mb-1 {{ ($status ?? '') == 'all' || empty($status) ? 'active font-weight-bold shadow-sm' : '' }}">
                                        <i class="fas fa-list mr-1"></i> All Requests
                                        @if (isset($counts['all']))
                                            <span class="badge badge-light ml-1">{{ $counts['all'] }}</span>
                                        @endif
                                    </a>
                                    <a href="{{ route('admin.redemption', ['status' => 'approved']) }}"
                                       class="btn btn-sm btn-success mb-1 {{ ($status ?? '') == 'approved' ? 'active font-weight-bold shadow-sm' : '' }}">
                                        <i class="fas fa-check mr-1"></i> Approved
                                        @if (isset($counts['approved']))
                                            <span class="badge badge-light ml-1">{{ $counts['approved'] }}</span>
                                        @endif
                                    </a>
                                    <a href="{{ route('admin.redemption', ['status' => 'rejected']) }}"
                                       class="btn btn-sm btn-danger mb-1 {{ ($status ?? '') == 'rejected' ? 'active font-weight-bold shadow-sm' : '' }}">
                                        <i class="fas fa-times mr-1"></i> Rejected
                                        @if (isset($counts['rejected']))
                                            <span class="badge badge-light ml-1">{{ $counts['rejected'] }}</span>
                                        @endif
                                    </a>
                                </div>
                            </div>
                        </div>

                        @if ($redeemRequests->count() > 0)
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
                                                <td>
                                                <a href="{{ route('admin.redemption.show', ['id' => $request->id, 'redirect_to' => request()->fullUrl()]) }}" class="font-weight-bold text-primary">
                                                    #{{ $request->id }}
                                                </a>
                                            </td>
                                            <td>
                                                <div class="user-info">
                                                    <strong>
                                                        @if (optional($request->user)->deleted_at)
                                                            <span class="text-danger">{{ optional($request->user)->name ?? 'Guest User' }}</span>
                                                        @else
                                                            <a href="{{ route('admin.users.show', ['user' => optional($request->user)->id, 'redirect_to' => request()->fullUrl()]) }}"
                                                                class="user-link">
                                                                {{ optional($request->user)->name }}
                                                            </a>
                                                        @endif
                                                    </strong>
                                                    @if(optional($request->user)->mobile_number)
                                                        <br><small class="text-muted"><i class="fas fa-phone-alt mr-1"></i>{{ optional($request->user)->mobile_number }}</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div class="transaction-info" style="font-size: 12px">
                                                    <span class="badge badge-info mb-1">
                                                        {{ ucfirst($request->transaction_type) }}
                                                    </span>
                                                    @if ($request->transaction_id)
                                                        <div class="transaction-detail mt-1">
                                                            <i class="fas fa-hashtag text-muted"></i>
                                                            <span class="text-dark font-weight-bold">
                                                                {{ $request->transaction_id }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                    @if ($request->remark)
                                                        <div class="transaction-detail mt-1">
                                                            <i class="fas fa-comment text-muted"></i>
                                                            <span class="text-dark font-italic">
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
                                                        @if(optional($request->gift)->image)
                                                            <a href="{{ asset('images/gifts/' . basename(optional($request->gift)->image ?? 'default.jpg')) }}"
                                                                target="_blank">
                                                                <img src="{{ asset('images/gifts/' . basename(optional($request->gift)->image ?? 'default.jpg')) }}"
                                                                    alt="Gift Image" class="img-thumbnail"
                                                                    style="max-width: 80px; height: auto; margin-top: 5px;">
                                                            </a>
                                                        @endif
                                                    </div>
                                                @elseif ($request->transaction_type === 'product')
                                                    <div>
                                                        <strong>Product:</strong>
                                                        {{ optional($request->product)->name ?? 'No Product' }}<br>
                                                        @if(optional($request->product)->image)
                                                            <a href="{{ asset('images/products/' . basename(optional($request->product)->image ?? 'default.jpg')) }}"
                                                                target="_blank">
                                                                <img src="{{ asset('images/products/' . basename(optional($request->product)->image ?? 'default.jpg')) }}"
                                                                    alt="Product Image" class="img-thumbnail"
                                                                    style="max-width: 80px; height: auto; margin-top: 5px;">
                                                            </a>
                                                        @endif
                                                    </div>
                                                @elseif ($request->transaction_type === 'account')
                                                    <div class="compact-details">
                                                        <strong>Bank Details:</strong>
                                                        <ul class="list-unstyled mb-0" style="font-size: 13px;">
                                                            <li><i class="fas fa-university text-muted mr-1"></i>{{ optional($request->user)->bank_name ?? 'N/A' }}</li>
                                                            <li><i class="fas fa-credit-card text-muted mr-1"></i>{{ optional($request->user)->account_number ?? 'N/A' }}</li>
                                                            <li><i class="fas fa-code-branch text-muted mr-1"></i>{{ optional($request->user)->ifsc_code ?? 'N/A' }}</li>
                                                        </ul>
                                                    </div>
                                                @elseif ($request->transaction_type === 'upi')
                                                    <div>
                                                        <strong><i class="fas fa-mobile-alt text-muted mr-1"></i> UPI:</strong>
                                                        <span>{{ optional($request->user)->mobile_number ?? 'N/A' }}</span>
                                                    </div>
                                                @elseif ($request->transaction_type === 'cash')
                                                    <div>
                                                        <strong><i class="fas fa-money-bill-wave text-success mr-1"></i> Cash Payout</strong>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="font-weight-bold text-dark">{{ number_format($request->points) }}</span> pts
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.redemption', ['status' => $request->status]) }}">
                                                    <span class="badge badge-{{ $request->status === 'approved' ? 'success' : ($request->status === 'rejected' ? 'danger' : 'warning') }}">
                                                        {{ ucfirst($request->status) }}
                                                    </span>
                                                </a>
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ $request->created_at->format('d/m/Y H:i') }}</small>
                                                @if ($request->status === 'approved' && $request->approved_at)
                                                    <div style="color: #28a745; font-size: 12px; font-style: italic; margin-top: 4px;">
                                                        <i class="fas fa-check-circle"></i> Approved: {{ date('d/m/Y H:i', strtotime($request->approved_at)) }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td style="min-width: 170px;">
                                                <div class="d-flex align-items-center flex-wrap" style="gap: 5px;">
                                                    <a href="{{ route('admin.redemption.show', ['id' => $request->id, 'redirect_to' => request()->fullUrl()]) }}"
                                                       class="btn btn-info btn-sm" title="View / Open Request Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>

                                                    @if (!optional($request->user)->deleted_at && $request->status === 'pending')
                                                        @if (in_array($request->transaction_type, ['upi', 'account']))
                                                            <form action="{{ route('admin.redemption.approve', $request->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Approve this redemption request?');">
                                                                @csrf
                                                                <input type="hidden" name="redirect_to" value="{{ request()->fullUrl() }}">
                                                                <div class="input-group input-group-sm mb-1" style="max-width: 150px;">
                                                                    <input type="text" name="transaction_id" class="form-control form-control-sm" placeholder="Txn ID" value="{{ $request->transaction_id ?? '' }}" required>
                                                                    <div class="input-group-append">
                                                                        <button type="submit" class="btn btn-success btn-sm" title="Approve">
                                                                            <i class="fas fa-check"></i>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        @else
                                                            <form action="{{ route('admin.redemption.approve', $request->id) }}" method="POST" style="display:inline;">
                                                                @csrf
                                                                <input type="hidden" name="redirect_to" value="{{ request()->fullUrl() }}">
                                                                <button type="submit" class="btn btn-success btn-sm" title="Approve" onclick="return confirm('Are you sure you want to approve this request?');">
                                                                    <i class="fas fa-check"></i>
                                                                </button>
                                                            </form>
                                                        @endif

                                                        <form action="{{ route('admin.redemption.reject', $request->id) }}" method="POST" style="display:inline;">
                                                            @csrf
                                                            <input type="hidden" name="redirect_to" value="{{ request()->fullUrl() }}">
                                                            <button type="submit" class="btn btn-danger btn-sm" title="Reject" onclick="return confirm('Are you sure you want to reject this request and refund points?');">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="empty-state text-center py-5">
                                <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                                <h4 class="text-muted font-weight-bold">No {{ ucfirst($status ?? 'Redemption') }} Requests Found</h4>
                                <p class="text-muted mb-0">
                                    @if(($status ?? '') == 'pending')
                                        Great job! There are no pending requests waiting for approval.
                                    @else
                                        There are currently no {{ $status ?? '' }} redemption requests to display.
                                    @endif
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
