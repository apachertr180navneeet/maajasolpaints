@extends('layouts.app', [
    'class' => '',
    'activeSection' => 'slider',
])

@section('content')
    <div class="content">
        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger" role="alert">
                {{ session('error') }}
            </div>
        @endif
        <div class="row">
            <div class="col-md-12">
                <form
                    action="{{ isset($slider) ? route('admin.sliders.update', $slider->id) : route('admin.sliders.store') }}"
                    method="POST" enctype="multipart/form-data">

                    @csrf
                    @if (isset($slider))
                        @method('PUT')
                    @endif

                    <div class="card">
                        <div class="card-header">
                            <h5 class="title">{{ isset($slider) ? __('Edit Slider') : __('Add Slider') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label">{{ __('Title') }}</label>
                                        <input type="text" name="title" class="form-control" placeholder="Title"
                                            value="{{ isset($slider) ? $slider->title : old('title') }}" required>
                                        @if ($errors->has('title'))
                                            <span class="invalid-feedback" style="display: block;" role="alert">
                                                <strong>{{ $errors->first('title') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label">{{ __('Link') }}</label>
                                        <input type="text" name="link" class="form-control" placeholder="Link"
                                            value="{{ isset($slider) ? $slider->link : old('link') }}">
                                        @if ($errors->has('link'))
                                            <span class="invalid-feedback" style="display: block;" role="alert">
                                                <strong>{{ $errors->first('link') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>


                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label">{{ __('Expiry Date') }}</label>
                                        <input type="date" name="expiry_date" class="form-control"
                                            value="{{ isset($slider) ? $slider->expiry_date : old('expiry_date') }}">
                                        @if ($errors->has('expiry_date'))
                                            <span class="invalid-feedback" style="display: block;" role="alert">
                                                <strong>{{ $errors->first('expiry_date') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label">{{ __('Sequence') }}</label>
                                        <input type="number" name="sequence" class="form-control" placeholder="Sequence"
                                            value="{{ isset($slider) ? $slider->sequence : old('sequence', 0) }}" required>
                                        @if ($errors->has('sequence'))
                                            <span class="invalid-feedback" style="display: block;" role="alert">
                                                <strong>{{ $errors->first('sequence') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label">{{ __('Status') }}</label>
                                        <select name="status" class="form-control">
                                            <option value="active"
                                                {{ isset($slider) && $slider->status == 'active' ? 'selected' : '' }}>
                                                Active
                                            </option>
                                            <option value="inactive"
                                                {{ isset($slider) && $slider->status == 'inactive' ? 'selected' : '' }}>
                                                Inactive
                                            </option>
                                        </select>
                                        @if ($errors->has('status'))
                                            <span class="invalid-feedback" style="display: block;" role="alert">
                                                <strong>{{ $errors->first('status') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                </div>
                            </div>

                            <br>
                            <br>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="custom-file">
                                            <input type="file" name="image" class="custom-file-input" id="sliderImage">
                                            <label class="custom-file-label"
                                                for="sliderImage">{{ __('Choose file') }}</label>
                                        </div>
                                        @if (isset($slider) && $slider->image)
                                            <div class="mt-2">
                                                <label class="col-form-label">{{ __('Current Image') }}</label>
                                                <a href="{{ asset('images/sliders/' . basename($slider->image)) }}"
                                                    target="_blank">
                                                    <img src="{{ asset('images/sliders/' . basename($slider->image)) }}"
                                                        alt="Slider Image" class="img-thumbnail"
                                                        style="max-width: 15%; height: auto;">
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <button type="submit" class="btn btn-info btn-round">
                                        {{ isset($slider) ? __('Save Changes') : __('Add Slider') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
