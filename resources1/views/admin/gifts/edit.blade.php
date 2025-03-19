@extends('layouts.app', [
    'class' => '',
    'activeSection' => 'gifts',
])

@section('content')
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <form action="{{ isset($gift) ? route('admin.gift.update', $gift->id) : route('admin.gift.store') }}"
                    method="POST" enctype="multipart/form-data">
                    @csrf
                    @if (isset($gift))
                        @method('PUT')
                    @endif
                    <div class="card">
                        <div class="card-header">
                            <h5 class="title">{{ isset($gift) ? __('Edit Gift') : __('Add Gift') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label">{{ __('Name') }}</label>
                                        <input type="text" name="name" class="form-control" placeholder="Gift Name"
                                            value="{{ isset($gift) ? $gift->name : old('name') }}" required>
                                        @if ($errors->has('name'))
                                            <span class="invalid-feedback" style="display: block;" role="alert">
                                                <strong>{{ $errors->first('name') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label">{{ __('Points') }}</label>
                                        <input type="number" name="points" class="form-control" placeholder="Points"
                                            value="{{ isset($gift) ? $gift->points : old('points') }}" required>
                                        @if ($errors->has('points'))
                                            <span class="invalid-feedback" style="display: block;" role="alert">
                                                <strong>{{ $errors->first('points') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <br>
                            <br>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="custom-file">
                                            <input type="file" name="image" class="custom-file-input" id="giftImage">
                                            <label class="custom-file-label" for="giftImage">{{ __('Choose file') }}</label>
                                        </div>
                                        @if (isset($gift) && $gift->image)
                                            <div class="mt-2">
                                                <label class="col-form-label">{{ __('Current Image') }}</label>
                                                <a href="{{ asset('images/gifts/' . basename($gift->image)) }}"
                                                    target="_blank">
                                                    <img src="{{ asset('images/gifts/' . basename($gift->image)) }}"
                                                        alt="Gift Image" class="img-thumbnail"
                                                        style="max-width: 15%; height: auto;">
                                                </a>
                                            </div>
                                        @endif
                                        @if ($errors->has('image'))
                                            <span class="invalid-feedback" style="display: block;" role="alert">
                                                <strong>{{ $errors->first('image') }}</strong>
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
                                        class="btn btn-info btn-round">{{ isset($gift) ? __('Save Changes') : __('Create Gift') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Custom file input label update
        document.getElementById('giftImage').addEventListener('change', function(event) {
            var input = event.target;
            var label = input.nextElementSibling;
            var fileName = input.files[0] ? input.files[0].name : 'Choose file';
            label.textContent = fileName;
        });
    </script>
@endsection
