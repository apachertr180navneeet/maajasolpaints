@extends('layouts.app', [
    'class' => '',
    'activeSection' => 'users',
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
                                    <h3 class="mb-0">{{ $title ?? 'Users' }}</h3>                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table id="datatable" class="table align-items-center table-flush">
                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col">User</th>
                                        <th scope="col">Image</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Created At</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($users as $user)
                                        <tr>
                                            <td>
                                                @if (file_exists(public_path($user->profile_image)) && $user->profile_image)
                                                    <img class="profileImage" src="{{ asset($user->profile_image) }}" alt="Profile Image"
                                                        width="50" height="50" style="border-radius: 50%; cursor: pointer;">
                                                @else
                                                    <img class="profileImage" src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}"
                                                        alt="Default Profile Image" width="50" height="50"
                                                        style="border-radius: 50%; cursor: pointer;">
                                                @endif
                                                <strong>{{ $user->name }}</strong><br>
                                                {{ $user->email }}<br>
                                                {{ $user->mobile_number }}<br>
                                                <span style="color: {{ $user->balance >= 0 ? 'green' : 'red' }};">
                                                    Available Balance: {{ $user->balance }}
                                                </span> <br>
                                                <span style="color: {{ $user->gift_balance >= 0 ? 'green' : 'red' }};">
                                                    Gift Balance: {{ $user->gift_balance }}
                                                </span>
                                                <br>
                                                Lifetime Balance: {{ $user->lifetime_balance ?? 0.0 }}
                                            </td>
                                            <td>
                                                @if ($user->aadhaar_image)
                                                    <a href="{{ asset($user->aadhaar_image) }}" target="_blank"
                                                        class="btn btn-sm btn-primary mb-2">
                                                        <i class="fas fa-id-card"></i> View Aadhaar
                                                    </a>
                                                @else
                                                    <span class="text-muted">No Aadhaar Image</span>
                                                @endif
                                                <br>
                                                @if ($user->bank_cheque_image)
                                                    <a href="{{ asset($user->bank_cheque_image) }}" target="_blank"
                                                        class="btn btn-sm btn-info">
                                                        <i class="fas fa-money-check"></i> View Cheque
                                                    </a>
                                                @else
                                                    <span class="text-muted">No Cheque Image</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge 
                                                    @if ($user->status == 'active') bg-success
                                                    @elseif($user->status == 'inactive') bg-warning
                                                    @elseif($user->status == 'blocked') bg-danger
                                                    @elseif($user->status == 'pending') bg-secondary @endif">
                                                    {{ ucfirst($user->status) }}
                                                </span>
                                            </td>
                                            <td> 
                                                {{ $user->created_at ? $user->created_at->format('d/m/Y H:i') : 'N/A' }}
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.users.show', $user->id) }}"
                                                    class="btn btn-info btn-sm" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.users.edit', $user->id) }}"
                                                    class="btn btn-warning btn-sm" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST"
                                                    style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm"
                                                        onclick="return confirm('Are you sure you want to delete this user?')"
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

    <!-- Bootstrap Modal -->
    <div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Profile Image</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <img id="zoomedImage" src="" alt="Profile Image" style="width: 100%; max-height: 500px; border-radius: 10px;">
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery Script for Zoom Effect -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            $(".profileImage").click(function () { // Changed ID to class
                var imgSrc = $(this).attr("src");
                $("#zoomedImage").attr("src", imgSrc);
                $("#imageModal").modal("show");
            });
        });
    </script>
@endsection
