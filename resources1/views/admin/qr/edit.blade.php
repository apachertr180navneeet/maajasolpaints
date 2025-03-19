@extends('layouts.app', [
    'class' => '',
    'activeSection' => 'qr',
])

@section('content')
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <form action="{{ isset($qr) ? route('admin.qr.update', $qr->id) : route('admin.qr.store') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @if (isset($qr))
                        @method('PUT')
                    @endif
                    <div class="card">
                        <div class="card-header">
                            <h5 class="title">{{ isset($qr) ? __('Edit QR Code') : __('Add QR Code') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label">{{ __('QR ID') }}</label>
                                        <input type="text" name="qr_id" class="form-control" placeholder="QR ID"
                                            value="{{ isset($qr) ? $qr->qr_id : old('qr_id') }}" required>
                                        @if ($errors->has('qr_id'))
                                            <span class="invalid-feedback" style="display: block;" role="alert">
                                                <strong>{{ $errors->first('qr_id') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label">{{ __('Points') }}</label>
                                        <input type="number" name="points" class="form-control" placeholder="Points"
                                            value="{{ isset($qr) ? $qr->points : old('points') }}" required>
                                        @if ($errors->has('points'))
                                            <span class="invalid-feedback" style="display: block;" role="alert">
                                                <strong>{{ $errors->first('points') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label">{{ __('Remark') }}</label>
                                        <input type="text" name="remark" class="form-control" placeholder="Remark"
                                            value="{{ isset($qr) ? $qr->remark : old('remark') }}">
                                        @if ($errors->has('remark'))
                                            <span class="invalid-feedback" style="display: block;" role="alert">
                                                <strong>{{ $errors->first('remark') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label">{{ __('Is Used') }}</label>
                                        <select name="is_used" class="form-control">
                                            <option value="0" {{ isset($qr) && !$qr->is_used ? 'selected' : '' }}>No
                                            </option>
                                            <option value="1" {{ isset($qr) && $qr->is_used ? 'selected' : '' }}>Yes
                                            </option>
                                        </select>
                                        @if ($errors->has('is_used'))
                                            <span class="invalid-feedback" style="display: block;" role="alert">
                                                <strong>{{ $errors->first('is_used') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label">{{ __('Used By') }}</label>
                                        <select name="used_by" class="form-control">
                                            <option value=""
                                                {{ isset($qr) && is_null($qr->used_by) ? 'selected' : '' }}>Not Used
                                            </option>
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}"
                                                    {{ isset($qr) && $qr->used_by == $user->id ? 'selected' : '' }}>
                                                    {{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('used_by'))
                                            <span class="invalid-feedback" style="display: block;" role="alert">
                                                <strong>{{ $errors->first('used_by') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <button type="submit"
                                        class="btn btn-info btn-round">{{ isset($qr) ? __('Save Changes') : __('Create QR Code') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
