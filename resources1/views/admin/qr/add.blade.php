@extends('layouts.app', [
    'class' => '',
    'activeSection' => 'qr',
])

@section('content')
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <form action="{{ route('admin.qr.storeBatch') }}" method="POST">
                    @csrf
                    <div class="card">
                        <div class="card-header">
                            <h5 class="title">{{ __('Add Multiple QR Codes') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label">{{ __('Number of QRs') }}</label>
                                        <input type="number" name="number_of_qrs" class="form-control"
                                            placeholder="Enter number of QRs" value="{{ old('number_of_qrs') }}" required>
                                        @if ($errors->has('number_of_qrs'))
                                            <span class="invalid-feedback" style="display: block;" role="alert">
                                                <strong>{{ $errors->first('number_of_qrs') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label">{{ __('Points') }}</label>
                                        <input type="number" name="points" class="form-control" placeholder="Points"
                                            value="{{ old('points') }}" required>
                                        @if ($errors->has('points'))
                                            <span class="invalid-feedback" style="display: block;" role="alert">
                                                <strong>{{ $errors->first('points') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-center">
                            <button type="submit" class="btn btn-info btn-round">{{ __('Generate QR Codes') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
