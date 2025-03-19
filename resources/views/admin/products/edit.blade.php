@extends('layouts.app', [
    'class' => '',
    'activeSection' => 'products',
])

@section('content')
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <form action="{{ isset($product) ? route('admin.product.update', $product->id) : route('admin.product.store') }}"
                    method="POST" enctype="multipart/form-data">
                    @csrf
                    @if (isset($product))
                        @method('PUT')
                    @endif
                    <div class="card">
                        <div class="card-header">
                            <h5 class="title">{{ isset($product) ? __('Edit Product') : __('Add Product') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label">{{ __('Name') }}</label>
                                        <input type="text" name="name" class="form-control" placeholder="Product Name"
                                            value="{{ isset($product) ? $product->name : old('name') }}" required {{ $disabled ?? false ? 'disabled' : '' }}>
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
                                            value="{{ isset($product) ? $product->points : old('points') }}" required {{ $disabled ?? false ? 'disabled' : '' }}>
                                        @if ($errors->has('points'))
                                            <span class="invalid-feedback" style="display: block;" role="alert">
                                                <strong>{{ $errors->first('points') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label">{{ __('Worth Price') }}</label>
                                        <input type="number" name="worth_price" class="form-control" placeholder="Worth Price"
                                            value="{{ isset($gift) ? $gift->worth_price : old('worth_price') }}" required>
                                        @if ($errors->has('worth_price'))
                                            <span class="invalid-feedback" style="display: block;" role="alert">
                                                <strong>{{ $errors->first('worth_price') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                {{-- <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label">{{ __('Description') }}</label>
                                        <textarea name="description" class="form-control" placeholder="Product Description" rows="3" {{ $disabled ?? false ? 'disabled' : '' }}>{{ isset($product) ? $product->description : old('description') }}</textarea>
                                        @if ($errors->has('description'))
                                            <span class="invalid-feedback" style="display: block;" role="alert">
                                                <strong>{{ $errors->first('description') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div> --}}
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label">{{ __('Expiry Date') }}</label>
                                        <input type="date" name="expiry_date" class="form-control"
                                            value="{{ isset($product) ? $product->expiry_date->format('Y-m-d') : old('expiry_date') }}" {{ $disabled ?? false ? 'disabled' : '' }}>
                                        @if ($errors->has('expiry_date'))
                                            <span class="invalid-feedback" style="display: block;" role="alert">
                                                <strong>{{ $errors->first('expiry_date') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="custom-file">
                                            <input type="file" name="image" class="custom-file-input" id="productImage" {{ $disabled ?? false ? 'disabled' : '' }}>
                                            <label class="custom-file-label" for="productImage">{{ __('Choose file') }}</label>
                                        </div>
                                        @if (isset($product) && $product->image)
                                            <div class="mt-2">
                                                <label class="col-form-label">{{ __('Current Image') }}</label>
                                                <a href="{{ asset('images/products/' . basename($product->image)) }}"
                                                    target="_blank">
                                                    <img src="{{ asset('images/products/' . basename($product->image)) }}"
                                                        alt="Product Image" class="img-thumbnail"
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
                                    <button type="submit" class="btn btn-info btn-round" {{ $disabled ?? false ? 'disabled' : '' }}>
                                        {{ isset($product) ? __('Save Changes') : __('Create Product') }}
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

@section('scripts')
    <script>
        // Custom file input label update
        document.getElementById('productImage').addEventListener('change', function(event) {
            var input = event.target;
            var label = input.nextElementSibling;
            var fileName = input.files[0] ? input.files[0].name : 'Choose file';
            label.textContent = fileName;
        });
    </script>
@endsection
