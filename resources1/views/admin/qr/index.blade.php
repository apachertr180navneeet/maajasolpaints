@extends('layouts.app', [
    'class' => '',
    'activeSection' => 'qr',
])
@section('content')
    <style>
        .fixed-height {
            height: 38px;
            /* Set the height to match both input and button */
        }
    </style>
    <div class="content">
        <div class="container-fluid mt--7">
            <div class="row">
                <div class="col">
                    <div class="card shadow">
                        <div class="card-header border-0">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <h3 class="mb-0">Active QR Records</h3>
                                </div>
                                <div class="col-4 d-flex justify-content-end">
                                    <a href="{{ route('admin.qr.create') }}" class="btn btn-primary mr-2">Create QR</a>
                                </div>
                            </div>
                            <div class="row align-items-center">

                                <div class="offset-4 col-4 d-flex">
                                    <form action="{{ route('admin.qr.exportPdf') }}" method="GET">
                                        <div class="d-flex align-items-center">
                                            <input type="number" name="count" value="10" class="form-control"
                                                placeholder="No. of Records" style="height: 38px; width: 120px;">
                                            <button type="submit" class="btn btn-secondary">Export PDF</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-items-center table-flush" id="datatable">
                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col">QR ID</th>
                                        <th scope="col">QR Image</th>
                                        <th scope="col">Points</th>
                                        <th scope="col">Created At</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($qrs as $qr)
                                        <tr>
                                            <td>{{ $qr->qr_id }}</td>
                                            <td>
                                                @if ($qr->qr_image)
                                                    <img src="{{ asset('images/qrcodes/' . $qr->qr_image) }}" alt="QR Code"
                                                        style="width:100px;">
                                                @else
                                                    No Image Available
                                                @endif
                                            </td>
                                            <td>{{ $qr->points }}</td>
                                            <td>{{ $qr->created_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                <a href="{{ route('admin.qr.edit', $qr->id) }}"
                                                    class="btn btn-warning btn-sm" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.qr.destroy', $qr->id) }}" method="POST"
                                                    style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm"
                                                        onclick="return confirm('Are you sure you want to delete this QR record?')"
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

                        {{-- <div class="card-footer py-4">
                            <nav class="d-flex justify-content-end" aria-label="Pagination">
                                {{ $qrs->links('pagination::bootstrap-4') }}
                            </nav>
                        </div> --}}

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
