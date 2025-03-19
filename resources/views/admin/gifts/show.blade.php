@extends('layouts.app', [
    'class' => '',
    'activeSection' => 'user',
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
                            <h5 class="title">{{ __('View Profile') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label">{{ __('Name') }}</label>
                                        <input type="text" name="name" class="form-control" placeholder="Name"
                                            value="{{ $user->name }}" required disabled>
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
                                            value="{{ $user->email }}" required disabled>
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
                                            placeholder="Mobile Number" value="{{ $user->mobile_number }}" required
                                            disabled>
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
                                            value="{{ $user->bank_name }}" disabled>
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
                                            placeholder="Account Number" value="{{ $user->account_number }}" disabled>
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
                                            value="{{ $user->ifsc_code }}" disabled>
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
                                            value="{{ $user->balance }}" disabled>
                                        @if ($errors->has('balance'))
                                            <span class="invalid-feedback" style="display: block;" role="alert">
                                                <strong>{{ $errors->first('balance') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label">{{ __('Address') }}</label>
                                        <input type="text" name="address" class="form-control" placeholder="Address"
                                            value="{{ $user->address }}" disabled>
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
                                            placeholder="Date of Birth" value="{{ $user->dob }}" disabled>
                                        @if ($errors->has('dob'))
                                            <span class="invalid-feedback" style="display: block;" role="alert">
                                                <strong>{{ $errors->first('dob') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="col-form-label">{{ __('Aadhaar Image') }}</label>
                                        @if ($user->aadhaar_image)
                                            <a href="{{ asset('images/aadhaar/' . basename($user->aadhaar_image)) }}"
                                                target="_blank">
                                                <img src="{{ asset('images/aadhaar/' . basename($user->aadhaar_image)) }}"
                                                    alt="Aadhaar Image" class="img-thumbnail"
                                                    style="width: 15%; height: auto;">
                                            </a>
                                        @else
                                            <p>No Image</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
@endsection
