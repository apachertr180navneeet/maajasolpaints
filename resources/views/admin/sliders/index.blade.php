@extends('layouts.app', [
    'class' => '',
    'activeSection' => $activeSection,
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
                                    <h3 class="mb-0">Sliders</h3>
                                </div>
                                <div class="col-4 text-right">
                                    <a href="{{ route('admin.sliders.create') }}" class="btn btn-primary">Add New Slider</a>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table align-items-center table-flush" id="datatable">
                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col">Title</th>
                                        <th scope="col">Image</th>
                                        <th scope="col">Sequence</th>
                                        <th scope="col">Expiry Date</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Created At</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($sliders as $slider)
                                        <tr>
                                            <td width="200">
                                                {{ $slider->title }} <br>
                                                <a href="{{ $slider->link }}" target="_blank">{{ $slider->link }}</a>
                                            </td>

                                            <td>
                                                @if ($slider->image)
                                                    <img src="{{ asset('images/sliders/' . basename($slider->image)) }}"
                                                        alt="Slider Image" style="max-width: 76px; height: auto;" />
                                                @else
                                                    No Image
                                                @endif
                                            </td>
                                            <td>
                                                <span>{{ $slider->sequence }} </span>
                                            </td>
                                            <td>
                                                <span>{{ $slider->expiry_date }} </span>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge
                                                @if ($slider->status == 'active') bg-success
                                                @elseif($slider->status == 'inactive')
                                                    bg-warning @endif
                                            ">
                                                    {{ ucfirst($slider->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $slider->created_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                <a href="{{ route('admin.sliders.edit', $slider->id) }}"
                                                    class="btn btn-warning btn-sm" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.sliders.destroy', $slider->id) }}"
                                                    method="POST" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm"
                                                        onclick="return confirm('Are you sure you want to delete this slider?')"
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
