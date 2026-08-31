@extends('layouts.app', [
    'class' => '',
    'activeSection' => 'redemption',
])

@section('content')
    <div class="content">
        <div class="container-fluid mt--7">
            <div class="row">
                <div class="col-md-12">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle mr-1"></i> {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <div class="card shadow">
                        <div class="card-header border-0 d-flex justify-content-between align-items-center flex-wrap">
                            <div>
                                <h3 class="mb-0 d-inline-block">
                                    Redemption Request #{{ $transaction->id }}
                                </h3>
                                <span class="badge ml-2 badge-{{ $transaction->status === 'approved' ? 'success' : ($transaction->status === 'rejected' ? 'danger' : 'warning') }}" style="font-size: 14px;">
                                    {{ ucfirst($transaction->status) }}
                                </span>
                            </div>
                            <div class="mt-2 mt-sm-0">
                                @if ($nextPending)
                                    <a href="{{ route('admin.redemption.show', ['id' => $nextPending->id, 'redirect_to' => $redirectTo]) }}" 
                                       class="btn btn-outline-warning btn-sm mr-1" title="Go to next pending request">
                                        <i class="fas fa-step-forward mr-1"></i> Next Pending (#{{ $nextPending->id }})
                                    </a>
                                @endif
                                <a href="{{ $redirectTo }}" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-arrow-left mr-1"></i> {{ __('Back to Requests') }}
                                </a>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <!-- User Information -->
                                <div class="col-lg-6 col-md-12 mb-4">
                                    <div class="card border h-100">
                                        <div class="card-header bg-light">
                                            <h5 class="mb-0 text-dark"><i class="fas fa-user mr-2 text-primary"></i>User Details</h5>
                                        </div>
                                        <div class="card-body">
                                            @if ($transaction->user)
                                                <div class="d-flex align-items-center mb-3">
                                                    @if (file_exists(public_path($transaction->user->profile_image)) && $transaction->user->profile_image)
                                                        <img src="{{ asset($transaction->user->profile_image) }}" alt="Profile"
                                                            width="55" height="55" style="border-radius: 50%;" class="mr-3 shadow-sm">
                                                    @else
                                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($transaction->user->name) }}"
                                                            alt="Avatar" width="55" height="55"
                                                            style="border-radius: 50%;" class="mr-3 shadow-sm">
                                                    @endif
                                                    <div>
                                                        <h5 class="mb-0 font-weight-bold">
                                                            <a href="{{ route('admin.users.show', ['user' => $transaction->user->id, 'redirect_to' => request()->fullUrl()]) }}" class="text-primary">
                                                                {{ $transaction->user->name }}
                                                            </a>
                                                        </h5>
                                                        <small class="text-muted"><i class="fas fa-envelope mr-1"></i>{{ $transaction->user->email }}</small><br>
                                                        <small class="text-muted"><i class="fas fa-phone mr-1"></i>{{ $transaction->user->mobile_number }}</small>
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="row">
                                                    <div class="col-6 mb-2">
                                                        <small class="text-muted d-block">Available Points:</small>
                                                        <span class="font-weight-bold text-success" style="font-size: 16px;">
                                                            {{ number_format($transaction->user->balance, 2) }}
                                                        </span>
                                                    </div>
                                                    <div class="col-6 mb-2">
                                                        <small class="text-muted d-block">Gift Balance:</small>
                                                        <span class="font-weight-bold text-info" style="font-size: 16px;">
                                                            {{ number_format($transaction->user->gift_balance, 2) }}
                                                        </span>
                                                    </div>
                                                    <div class="col-6">
                                                        <small class="text-muted d-block">User Status:</small>
                                                        <span class="badge badge-{{ $transaction->user->status === 'active' ? 'success' : 'secondary' }}">
                                                            {{ ucfirst($transaction->user->status) }}
                                                        </span>
                                                    </div>
                                                    <div class="col-6">
                                                        <small class="text-muted d-block">Member Since:</small>
                                                        <span class="text-dark">{{ $transaction->user->created_at ? $transaction->user->created_at->format('d M Y') : 'N/A' }}</span>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="alert alert-warning mb-0">
                                                    <i class="fas fa-exclamation-triangle mr-1"></i> User account not found or was deleted.
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Request Summary -->
                                <div class="col-lg-6 col-md-12 mb-4">
                                    <div class="card border h-100">
                                        <div class="card-header bg-light">
                                            <h5 class="mb-0 text-dark"><i class="fas fa-info-circle mr-2 text-info"></i>Redemption Summary</h5>
                                        </div>
                                        <div class="card-body">
                                            <table class="table table-sm table-borderless">
                                                <tr>
                                                    <th class="text-muted" style="width: 40%;">Redemption Type:</th>
                                                    <td>
                                                        <span class="badge badge-info px-2 py-1">
                                                            {{ strtoupper($transaction->transaction_type) }}
                                                        </span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th class="text-muted">Points Requested:</th>
                                                    <td>
                                                        <span class="font-weight-bold text-danger" style="font-size: 18px;">
                                                            {{ number_format($transaction->points) }} pts
                                                        </span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th class="text-muted">Request Date:</th>
                                                    <td>{{ $transaction->created_at->format('d M Y, h:i A') }}</td>
                                                </tr>
                                                @if ($transaction->transaction_id)
                                                    <tr>
                                                        <th class="text-muted">Transaction ID:</th>
                                                        <td class="font-weight-bold text-dark">{{ $transaction->transaction_id }}</td>
                                                    </tr>
                                                @endif
                                                @if ($transaction->remark)
                                                    <tr>
                                                        <th class="text-muted">Remark / Note:</th>
                                                        <td class="font-italic text-dark">{{ $transaction->remark }}</td>
                                                    </tr>
                                                @endif
                                                @if ($transaction->status === 'approved' && $transaction->approved_at)
                                                    <tr>
                                                        <th class="text-muted">Approved At:</th>
                                                        <td class="text-success font-weight-bold">
                                                            <i class="fas fa-check-circle mr-1"></i>{{ date('d M Y, h:i A', strtotime($transaction->approved_at)) }}
                                                        </td>
                                                    </tr>
                                                @endif
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Item / Payout Details -->
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <div class="card border">
                                        <div class="card-header bg-light">
                                            <h5 class="mb-0 text-dark"><i class="fas fa-gift mr-2 text-warning"></i>Payout / Item Details</h5>
                                        </div>
                                        <div class="card-body">
                                            @if ($transaction->transaction_type === 'gift')
                                                <div class="row align-items-center">
                                                    <div class="col-md-3 text-center">
                                                        @if (optional($transaction->gift)->image)
                                                            <a href="{{ asset('images/gifts/' . basename(optional($transaction->gift)->image)) }}" target="_blank">
                                                                <img src="{{ asset('images/gifts/' . basename(optional($transaction->gift)->image)) }}" 
                                                                     alt="Gift" class="img-thumbnail" style="max-height: 140px;">
                                                            </a>
                                                        @else
                                                            <span class="text-muted">No gift image</span>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-9">
                                                        <h4 class="font-weight-bold mb-1">{{ optional($transaction->gift)->name ?? 'Gift item' }}</h4>
                                                        <p class="text-muted mb-1">Required Points: <strong>{{ optional($transaction->gift)->points }} pts</strong></p>
                                                    </div>
                                                </div>
                                            @elseif ($transaction->transaction_type === 'product')
                                                <div class="row align-items-center">
                                                    <div class="col-md-3 text-center">
                                                        @if (optional($transaction->product)->image)
                                                            <a href="{{ asset('images/products/' . basename(optional($transaction->product)->image)) }}" target="_blank">
                                                                <img src="{{ asset('images/products/' . basename(optional($transaction->product)->image)) }}" 
                                                                     alt="Product" class="img-thumbnail" style="max-height: 140px;">
                                                            </a>
                                                        @else
                                                            <span class="text-muted">No product image</span>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-9">
                                                        <h4 class="font-weight-bold mb-1">{{ optional($transaction->product)->name ?? 'Product item' }}</h4>
                                                        <p class="text-muted mb-1">Product Points: <strong>{{ optional($transaction->product)->points }} pts</strong></p>
                                                    </div>
                                                </div>
                                            @elseif ($transaction->transaction_type === 'account')
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <small class="text-muted d-block">Bank Name:</small>
                                                        <h5 class="font-weight-bold">{{ optional($transaction->user)->bank_name ?? 'N/A' }}</h5>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <small class="text-muted d-block">Account Number:</small>
                                                        <h5 class="font-weight-bold">{{ optional($transaction->user)->account_number ?? 'N/A' }}</h5>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <small class="text-muted d-block">IFSC Code:</small>
                                                        <h5 class="font-weight-bold">{{ optional($transaction->user)->ifsc_code ?? 'N/A' }}</h5>
                                                    </div>
                                                </div>
                                                @if (optional($transaction->user)->bank_cheque_image)
                                                    <hr>
                                                    <small class="text-muted d-block mb-1">Cheque Proof:</small>
                                                    <a href="{{ asset($transaction->user->bank_cheque_image) }}" target="_blank" class="btn btn-outline-info btn-sm">
                                                        <i class="fas fa-file-image mr-1"></i> View Bank Cheque Image
                                                    </a>
                                                @endif
                                            @elseif ($transaction->transaction_type === 'upi')
                                                <div>
                                                    <small class="text-muted d-block">UPI Registered Mobile / ID:</small>
                                                    <h4 class="font-weight-bold text-dark">{{ optional($transaction->user)->mobile_number ?? 'N/A' }}</h4>
                                                </div>
                                            @elseif ($transaction->transaction_type === 'cash')
                                                <div>
                                                    <h5 class="font-weight-bold text-success mb-0"><i class="fas fa-money-bill-wave mr-2"></i>Cash Handover / Payout</h5>
                                                    <small class="text-muted">Unique cash transaction reference will be assigned automatically upon approval.</small>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Actions Section -->
                            @if ($transaction->status === 'pending')
                                <div class="card border border-warning shadow-sm">
                                    <div class="card-header bg-warning text-dark font-weight-bold">
                                        <i class="fas fa-tasks mr-2"></i>Process Request (Approve or Reject)
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <!-- Approve Form -->
                                            <div class="col-md-7 border-right">
                                                <h5 class="text-success font-weight-bold mb-3">
                                                    <i class="fas fa-check-circle mr-1"></i> Approve Request
                                                </h5>
                                                <form action="{{ route('admin.redemption.approve', $transaction->id) }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="redirect_to" value="{{ $redirectTo }}">
                                                    @if ($nextPending)
                                                        <input type="hidden" name="next_pending_id" value="{{ $nextPending->id }}">
                                                    @endif

                                                    @if (in_array($transaction->transaction_type, ['upi', 'account']))
                                                        <div class="form-group">
                                                            <label class="form-control-label font-weight-bold">
                                                                Bank / UPI Transaction Reference ID <span class="text-danger">*</span>
                                                            </label>
                                                            <input type="text" name="transaction_id" class="form-control" 
                                                                placeholder="e.g. UTR / Ref / Bank Txn Number" 
                                                                value="{{ old('transaction_id', $transaction->transaction_id) }}" required>
                                                        </div>
                                                    @endif

                                                    <div class="form-group">
                                                        <label class="form-control-label">Remark / Note (Optional)</label>
                                                        <textarea name="remark" class="form-control" rows="2" placeholder="Add approval remarks or instructions...">{{ old('remark', $transaction->remark) }}</textarea>
                                                    </div>

                                                    <button type="submit" class="btn btn-success btn-round" onclick="return confirm('Confirm approval for this redemption request?');">
                                                        <i class="fas fa-check mr-1"></i> Approve & Continue
                                                    </button>
                                                </form>
                                            </div>

                                            <!-- Reject Form -->
                                            <div class="col-md-5 mt-4 mt-md-0">
                                                <h5 class="text-danger font-weight-bold mb-3">
                                                    <i class="fas fa-times-circle mr-1"></i> Reject Request
                                                </h5>
                                                <form action="{{ route('admin.redemption.reject', $transaction->id) }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="redirect_to" value="{{ $redirectTo }}">

                                                    <div class="form-group">
                                                        <label class="form-control-label">Reason for Rejection (Optional)</label>
                                                        <textarea name="remark" class="form-control" rows="2" placeholder="e.g. Invalid bank details, account mismatch...">{{ old('remark') }}</textarea>
                                                    </div>

                                                    <button type="submit" class="btn btn-danger btn-round" onclick="return confirm('Are you sure you want to reject this request? Deducted points will be refunded to user.');">
                                                        <i class="fas fa-times mr-1"></i> Reject & Refund Points
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-{{ $transaction->status === 'approved' ? 'success' : 'danger' }} text-center py-3">
                                    <h4 class="mb-1 text-white font-weight-bold">
                                        <i class="fas fa-{{ $transaction->status === 'approved' ? 'check-circle' : 'times-circle' }} mr-2"></i>
                                        This redemption request is {{ strtoupper($transaction->status) }}.
                                    </h4>
                                    <p class="mb-0">No further action is required.</p>
                                </div>
                            @endif
                        </div>

                        <div class="card-footer text-center bg-light">
                            @if ($nextPending)
                                <a href="{{ route('admin.redemption.show', ['id' => $nextPending->id, 'redirect_to' => $redirectTo]) }}" 
                                   class="btn btn-warning btn-round mr-2">
                                    <i class="fas fa-step-forward mr-1"></i> Next Pending Request (#{{ $nextPending->id }})
                                </a>
                            @endif
                            <a href="{{ $redirectTo }}" class="btn btn-secondary btn-round">
                                <i class="fas fa-arrow-left mr-1"></i> Back to Pending Requests
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
