@extends('layouts.app', [
    'class' => '',
    'activeSection' => 'gift',
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
                                    <h3 class="mb-0">Gifts</h3>
                                </div>
                                <div class="col-4 text-right">
                                    <a href="{{ route('admin.gift.create') }}" class="btn btn-primary">Add Gift</a>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table align-items-center table-flush" id='datatable'>
                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col">Gift Name</th>
                                        <th scope="col">Image</th>
                                        <th scope="col">Points</th>
                                        <th scope="col">Created At</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($gifts as $gift)
                                        <tr>
                                            <td>{{ $gift->name }}</td>
                                            <td>
                                                @if ($gift->image)
                                                    <img src="{{ asset('images/gifts/' . $gift->image) }}"
                                                        alt="{{ $gift->name }}" style="width:100px;">
                                                @else
                                                    No Image Available
                                                @endif
                                            </td>
                                            <td>{{ $gift->points }}</td>
                                            <td>{{ $gift->created_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                <a href="{{ route('admin.gift.edit', $gift->id) }}"
                                                    class="btn btn-warning btn-sm" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.gift.destroy', $gift->id) }}" method="POST"
                                                    style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm"
                                                        onclick="return confirm('Are you sure you want to delete this gift?')"
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
