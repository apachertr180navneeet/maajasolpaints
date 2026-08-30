@extends('layouts.app', [
    'class' => '',
    'activeSection' => 'settings',
])

@section('content')
    <div class="content">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <form action="{{ route('admin.change.password.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="title">{{ __('Change Password') }}</h5>
                        </div>
                        <div class="card-body">
                            {{-- Current Password --}}
                            <div class="form-group">
                                <label for="current_password" class="font-weight-bold">{{ __('Current Password') }}</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    </div>
                                    <input type="password" name="current_password" id="current_password"
                                        class="form-control @error('current_password') is-invalid @enderror"
                                        placeholder="{{ __('Enter current password') }}" required>
                                    @error('current_password')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            {{-- New Password --}}
                            <div class="form-group">
                                <label for="password" class="font-weight-bold">{{ __('New Password') }}</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-key"></i></span>
                                    </div>
                                    <input type="password" name="password" id="password"
                                        class="form-control @error('password') is-invalid @enderror"
                                        placeholder="{{ __('Enter new password') }}" required>
                                    @error('password')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            {{-- Confirm Password --}}
                            <div class="form-group">
                                <label for="password_confirmation"
                                    class="font-weight-bold">{{ __('Confirm Password') }}</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-check"></i></span>
                                    </div>
                                    <input type="password" name="password_confirmation" id="password_confirmation"
                                        class="form-control"
                                        placeholder="{{ __('Re-enter new password') }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer text-center">
                            <button type="submit" class="btn btn-info btn-round">
                                <i class="fas fa-save"></i> {{ __('Update Password') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
