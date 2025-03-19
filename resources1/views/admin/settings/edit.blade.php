@extends('layouts.app', [
    'class' => '',
    'activeSection' => 'settings',
])

@php
    $methods = ['cash', 'account', 'gift', 'upi']; // Define available payment methods
@endphp

@section('content')
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <form action="{{ route('admin.settings.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="title">{{ __('Edit Settings') }}</h5>
                        </div>
                        <div class="card-body">
                            {{-- Dynamic settings form --}}
                            <div id="dynamic-form">
                                @foreach ($settings as $setting)
                                    <div class="row form-group setting-row align-items-center">
                                        <div class="col-md-5">
                                            <label for="key" class="font-weight-bold">{{ __('Key') }}</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-key"></i></span>
                                                </div>
                                                <input type="text" name="key[]" class="form-control"
                                                    value="{{ $setting->key }}" readonly required>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <label for="value" class="font-weight-bold">{{ __('Value') }}</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-edit"></i></span>
                                                </div>
                                                <input type="text" name="value[]" class="form-control"
                                                    value="{{ $setting->value }}" required>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Payment methods section --}}
                            <h6 class="mt-4">{{ __('Payment Methods') }}</h6>
                            <div class="form-group">
                                @foreach ($methods as $method)
                                    <label class="mr-3">
                                        <input type="checkbox" name="payment_methods[]" value="{{ $method }}"
                                            id="{{ $method }}" @if (in_array($method, $paymentMethods)) checked @endif>
                                        {{ ucfirst($method) }}
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="card-footer">
                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <button type="submit" class="btn btn-info btn-round">
                                        <i class="fas fa-save"></i> {{ __('Save Settings') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Template for a new row --}}
    <div style="display: none;" id="row-template">
        <div class="row form-group setting-row align-items-center">
            <div class="col-md-5">
                <label for="key" class="font-weight-bold">{{ __('Key') }}</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-key"></i></span>
                    </div>
                    <input type="text" name="key[]" class="form-control" placeholder="{{ __('Enter key') }}" required>
                </div>
            </div>
            <div class="col-md-5">
                <label for="value" class="font-weight-bold">{{ __('Value') }}</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-edit"></i></span>
                    </div>
                    <input type="text" name="value[]" class="form-control" placeholder="{{ __('Enter value') }}"
                        required>
                </div>
            </div>
            <div class="col-md-2 text-center">
                <button type="button" class="btn btn-danger btn-sm remove-row mt-4">
                    <i class="fas fa-trash-alt"></i> {{ __('Remove') }}
                </button>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add new setting row
            document.getElementById('add-row').addEventListener('click', function() {
                let template = document.getElementById('row-template').innerHTML;
                document.getElementById('dynamic-form').insertAdjacentHTML('beforeend', template);
            });

            // Remove setting row
            document.addEventListener('click', function(event) {
                if (event.target && event.target.classList.contains('remove-row')) {
                    event.target.closest('.setting-row').remove();
                }
            });
        });
    </script>
@endsection
