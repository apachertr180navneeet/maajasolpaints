@extends('layouts.app', [
    'class' => '',
    'activeSection' => 'products',
])
@section('content')
    <div class="content">
        <div class="container-fluid mt--7">
            <div class="row">
                <div class="col">
                    <div class="card shadow">
                        <div class="card-header border-0">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <h3 class="mb-0">Products</h3>
                                </div>
                                <div class="col-4 text-right">
                                    <a href="{{ route('admin.product.create') }}" class="btn btn-primary">Add Product</a>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-items-center table-flush" id='datatable'>
                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col">Product Name</th>
                                        <th scope="col">Image</th>
                                        <th scope="col">Points</th>
                                        <th scope="col">Expiry Date</th>
                                        <th scope="col">Created At</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($products as $product)
                                        <tr>
                                            <td>{{ $product->name }}</td>
                                            <td>
                                                @if ($product->image)
                                                    <img src="{{ asset('images/products/' . $product->image) }}"
                                                        alt="{{ $product->name }}" style="width:100px;">
                                                @else
                                                    No Image Available
                                                @endif
                                            </td>
                                            <td>{{ (int) $product->points }} <br> (Worth:
                                                {{ (int) ($product->worth_price) }})
                                            </td>                       
                                            <td>{{ $product->expiry_date ? $product->expiry_date->format('d/m/Y') : 'No Expiry Date' }}</td>
                                            <td>{{ $product->created_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                <a href="{{ route('admin.product.edit', $product->id) }}"
                                                    class="btn btn-warning btn-sm" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.product.destroy', $product->id) }}" method="POST"
                                                    style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm"
                                                        onclick="return confirm('Are you sure you want to delete this product?')"
                                                        title="Delete">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
