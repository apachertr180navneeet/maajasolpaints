@extends('layouts.app', [
    'class' => '',
    'activeSection' => 'used_qr',
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
                                    <h3 class="mb-0">Used QR Records</h3>
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
                                        <th scope="col">Remark</th>
                                        <th scope="col">Is Used</th>
                                        <th scope="col">Used By</th>
                                        <th scope="col">Created At</th>
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
                                            <td>{{ $qr->remark }}</td>
                                            <td>
                                                <span class="badge {{ $qr->is_used ? 'bg-success' : 'bg-warning' }}">
                                                    {{ $qr->is_used ? 'Yes' : 'No' }}
                                                </span>
                                            </td>
                                            <td>
                                                @if ($qr->used_by)
                                                    <a href="{{ route('admin.users.show', $qr->used_by) }}"
                                                        class="user-link">
                                                        {{ $qr->user->name ?? 'User not found' }}
                                                    </a>
                                                @else
                                                    Not Used
                                                @endif
                                            </td>
                                            <td>{{ $qr->created_at->format('d/m/Y H:i') }}</td>
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
