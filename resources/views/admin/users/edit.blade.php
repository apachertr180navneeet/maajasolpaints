@extends('layouts.app', [
    'class' => '',
    'activeSection' => 'users',
])

@section('content')
    <div class="content">
        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @endif
        @if (session('password_status'))
            <div class="alert alert-success" role="alert">
                {{ session('password_status') }}
            </div>
        @endif
        <div class="row">
            <div class="col-md-12">
                <form action="{{ route('admin.users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="card">
                        <div class="card-header">
                            <h5 class="title">{{ __('Edit Profile') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label">{{ __('Name') }}</label>
                                        <input type="text" name="name" class="form-control" placeholder="Name"
                                            value="{{ $user->name }}" required>
                                        @if ($errors->has('name'))
                                            <span class="invalid-feedback" style="display: block;" role="alert">
                                                <strong>{{ $errors->first('name') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label">{{ __('Email') }}</label>
                                        <input type="email" name="email" class="form-control" placeholder="Email"
                                            value="{{ $user->email }}" required>
                                        @if ($errors->has('email'))
                                            <span class="invalid-feedback" style="display: block;" role="alert">
                                                <strong>{{ $errors->first('email') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label">{{ __('Mobile Number') }}</label>
                                        <input type="text" name="mobile_number" class="form-control"
                                            placeholder="Mobile Number" value="{{ $user->mobile_number }}" required>
                                        @if ($errors->has('mobile_number'))
                                            <span class="invalid-feedback" style="display: block;" role="alert">
                                                <strong>{{ $errors->first('mobile_number') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label">{{ __('Bank Name') }}</label>
                                        <input type="text" name="bank_name" class="form-control" placeholder="Bank Name"
                                            value="{{ $user->bank_name }}">
                                        @if ($errors->has('bank_name'))
                                            <span class="invalid-feedback" style="display: block;" role="alert">
                                                <strong>{{ $errors->first('bank_name') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label">{{ __('Account Number') }}</label>
                                        <input type="text" name="account_number" class="form-control"
                                            placeholder="Account Number" value="{{ $user->account_number }}">
                                        @if ($errors->has('account_number'))
                                            <span class="invalid-feedback" style="display: block;" role="alert">
                                                <strong>{{ $errors->first('account_number') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label">{{ __('IFSC Code') }}</label>
                                        <input type="text" name="ifsc_code" class="form-control" placeholder="IFSC Code"
                                            value="{{ $user->ifsc_code }}">
                                        @if ($errors->has('ifsc_code'))
                                            <span class="invalid-feedback" style="display: block;" role="alert">
                                                <strong>{{ $errors->first('ifsc_code') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label">{{ __('Balance') }}</label>
                                        <input type="text" name="balance" class="form-control" placeholder="Balance"
                                            value="{{ $user->balance }}">
                                        @if ($errors->has('balance'))
                                            <span class="invalid-feedback" style="display: block;" role="alert">
                                                <strong>{{ $errors->first('balance') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                               <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label">{{ __('Gift Balance') }}</label>
                                        <input type="text" name="gift_balance" class="form-control" placeholder="Gift Balance"
                                            value="{{ $user->gift_balance }}">
                                        @if ($errors->has('gift_balance'))
                                            <span class="invalid-feedback" style="display: block;" role="alert">
                                                <strong>{{ $errors->first('gift_balance') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label">{{ __('Address') }}</label>
                                        <input type="text" name="address" class="form-control" placeholder="Address"
                                            value="{{ $user->address }}">
                                        @if ($errors->has('address'))
                                            <span class="invalid-feedback" style="display: block;" role="alert">
                                                <strong>{{ $errors->first('address') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label">{{ __('Date of Birth') }}</label>
                                        <input type="date" name="dob" class="form-control"
                                            placeholder="Date of Birth"
                                            value="{{ $user->dob ? $user->dob->format('Y-m-d') : '' }}">
                                        @if ($errors->has('dob'))
                                            <span class="invalid-feedback" style="display: block;" role="alert">
                                                <strong>{{ $errors->first('dob') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label">{{ __('Status') }}</label>
                                        <select name="status" class="form-control" required>
                                            <option value="" disabled>Select Status</option>
                                            <option value="active" {{ $user->status == 'active' ? 'selected' : '' }}>
                                                Active</option>
                                            <option value="inactive" {{ $user->status == 'inactive' ? 'selected' : '' }}>
                                                Inactive</option>
                                            <option value="blocked" {{ $user->status == 'blocked' ? 'selected' : '' }}>
                                                Blocked</option>
                                            <option value="pending" {{ $user->status == 'pending' ? 'selected' : '' }}>
                                                Pending</option>
                                        </select>
                                        @if ($errors->has('status'))
                                            <span class="invalid-feedback" style="display: block;" role="alert">
                                                <strong>{{ $errors->first('status') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-4">
                                <!-- Aadhaar Image Section -->
                                <div class="col-md-6">
                                    <div class="card shadow-sm">
                                        <div class="card-header bg-primary text-white p-1 w-100 mx-auto text-center">
                                            <h5 class="mb-0">{{ __('Aadhaar Image') }}</h5>
                                        </div>
                                        <div class="card-body">
                                            <!-- Aadhaar Image Upload -->
                                            <div class="form-group">
                                                <label for="aadhaarImage"
                                                    class="font-weight-bold">{{ __('Upload Aadhaar Image') }}</label>
                                                <div class="custom-file">
                                                    <input type="file" name="aadhaar_image" class="custom-file-input"
                                                        id="aadhaarImage">
                                                    <label class="custom-file-label"
                                                        for="aadhaarImage">{{ __('Choose Aadhaar file') }}</label>
                                                </div>
                                            </div>
                                            <!-- Aadhaar Image Preview -->
                                            <div class="text-center mt-3">
                                                @if ($user->aadhaar_image)
                                                    <label
                                                        class="font-weight-bold d-block">{{ __('Current Aadhaar Image') }}</label>
                                                    <a href="{{ asset('images/aadhaar/' . basename($user->aadhaar_image)) }}"
                                                        target="_blank">
                                                        <img src="{{ asset('images/aadhaar/' . basename($user->aadhaar_image)) }}"
                                                            alt="Aadhaar Image" class="img-thumbnail"
                                                            style="max-width: 150px; height: auto;">
                                                    </a>
                                                @else
                                                    <p class="text-muted">{{ __('No Aadhaar image uploaded yet') }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Bank Cheque Image Section -->
                                <div class="col-md-6">
                                    <div class="card shadow-sm">
                                        <div class="card-header bg-success text-white p-1 w-100 mx-auto text-center">
                                            <h5 class="mb-0">{{ __('Bank Cheque Image') }}</h5>
                                        </div>
                                        <div class="card-body">
                                            <!-- Bank Cheque Image Upload -->
                                            <div class="form-group">
                                                <label for="bankChequeImage"
                                                    class="font-weight-bold">{{ __('Upload Bank Cheque Image') }}</label>
                                                <div class="custom-file">
                                                    <input type="file" name="bank_cheque_image"
                                                        class="custom-file-input" id="bankChequeImage">
                                                    <label class="custom-file-label"
                                                        for="bankChequeImage">{{ __('Choose Bank Cheque file') }}</label>
                                                </div>
                                            </div>
                                            <!-- Bank Cheque Image Preview -->
                                            <div class="text-center mt-3">
                                                @if ($user->bank_cheque_image)
                                                    <label
                                                        class="font-weight-bold d-block">{{ __('Current Bank Cheque Image') }}</label>
                                                    <a href="{{ asset('images/cheque/' . basename($user->bank_cheque_image)) }}"
                                                        target="_blank">
                                                        <img src="{{ asset('images/cheque/' . basename($user->bank_cheque_image)) }}"
                                                            alt="Bank Cheque Image" class="img-thumbnail"
                                                            style="max-width: 150px; height: auto;">
                                                    </a>
                                                @else
                                                    <p class="text-muted">{{ __('No Bank Cheque image uploaded yet') }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer ">
                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <button type="submit"
                                        class="btn btn-info btn-round">{{ __('Save Changes') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
@endsection
