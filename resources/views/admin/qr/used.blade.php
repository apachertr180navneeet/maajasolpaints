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

                                        <th scope="col" class="font-weight-bold">QR ID</th>

                                        <th scope="col" class="font-weight-bold">QR Image</th>

                                        <th scope="col" class="font-weight-bold">Points</th>

                                        <th scope="col" class="font-weight-bold">Used By</th>

                                        <th scope="col" class="font-weight-bold">Product / QR Type</th>

                                        <th scope="col" class="font-weight-bold">Created At</th>

                                    </tr>

                                </thead>

                                <tbody>

                                    @foreach ($qrs as $qr)

                                        <tr>

                                            <td>{{ $qr->qr_id }}</td>

                                            <td>

                                                @if ($qr->qr_image)

                                                    <img src="{{ asset('images/qrcodes/' . $qr->qr_image) }}" alt="QR Code" style="width: 40px; height: 40px; object-fit: contain;">

                                                @else

                                                    <span class="text-muted">No Image</span>

                                                @endif

                                            </td>

                                            <td>{{ $qr->points }}</td>

                                            <td class="font-weight-bold">

                                                @if ($qr->is_used && $qr->used_by)

                                                    @php

                                                        $user = $qr->user;

                                                    @endphp

                                                    

                                                    @if ($user)

                                                        <a href="{{ route('admin.users.show', $user->id) }}" class="text-primary" style="color: #000 !important">

                                                            {{ $user->name }}

                                                        </a>

                                                    @else

                                                        <span class="text-muted" style="color: #000 !important">Not Available</span>

                                                    @endif

                                                    

                                                @else

                                                    <span class="text-muted" style="color: #000 !important">Not Used</span>

                                                @endif

                                            </td>

                                            

                                            <!-- Product or QR Type -->

                                            <td class="font-weight-bold">

                                                @if ($qr->is_product)

                                                    @if ($qr->product)

                                                        <a href="{{ route('admin.product.show', $qr->product_id) }}" style="color: #000">

                                                            {{ $qr->product->name }} (ID: {{ $qr->product_id }})

                                                        </a>

                                                    @else

                                                        <span class="text-danger">Product Deleted (ID: {{ $qr->product_id }})</span>

                                                    @endif

                                                @else

                                                    <span class="text-muted" style="color: #000">{{ $qr->qr_type }}</span>

                                                @endif

                                            </td>

                                            

                                            <td>

                                                <span class="text-dark">

                                                    {{ \Carbon\Carbon::parse($qr->created_at)->format('d/m/Y H:i') }}

                                                </span><br>

                                                <span class="text-muted">

                                                    Scanned At: {{ \Carbon\Carbon::parse($qr->scanned_at)->format('d/m/Y H:i') }}

                                                </span><br>

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

