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
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label">{{ __('QR Type') }}</label>
                                        <select name="qr_type" class="form-control" id="qr_type" required>
                                            <option value="">Select QR Type</option>
                                            <option value="cash" {{ old('qr_type') == 'cash' ? 'selected' : '' }}>Cash</option>
                                            <option value="gift" {{ old('qr_type') == 'gift' ? 'selected' : '' }}>Gift</option>
                                            <option value="product" {{ old('qr_type') == 'product' ? 'selected' : '' }}>Product</option>
                                        </select>
                                        @if ($errors->has('qr_type'))
                                            <span class="invalid-feedback" style="display: block;" role="alert">
                                                <strong>{{ $errors->first('qr_type') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6" id="product_dropdown" style="display:none;">
                                    <div class="form-group">
                                        <label class="col-form-label">{{ __('Select Product') }}</label>
                                        <select name="product_id" class="form-control">
                                            <option value="">Select Product</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                                    #{{ $product->id }} - {{ $product->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('product_id'))
                                            <span class="invalid-feedback" style="display: block;" role="alert">
                                                <strong>{{ $errors->first('product_id') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>

<script>
document.getElementById('qr_type').addEventListener('change', function() {
    var productDropdown = document.getElementById('product_dropdown');
    if(this.value === 'product') {
        productDropdown.style.display = 'block';
    } else {
        productDropdown.style.display = 'none';
    }
});
</script>                            </div>
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
