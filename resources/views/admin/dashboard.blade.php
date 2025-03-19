@extends('layouts.app')
@section('content')
    <style>
        .card-category {
            font-size: 16px !important;
        }
    </style>
    <div class="content">
        <div class="row">
            <!-- Gifts -->
            <div class="col-lg-3 col-md-4 col-sm-4">
                <div class="card card-stats">
                    <a href="{{ route('admin.gift') }}">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-5 col-md-4">
                                    <div class="icon-big text-center icon-warning">
                                        <i class="fas fa-gift text-danger"></i>
                                    </div>
                                </div>
                                <div class="col-7 col-md-8">
                                    <div class="numbers">
                                        <p class="card-category">Total <br> Gifts</p>
                                        <p class="card-title">{{ $giftCount }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <!-- QR Codes -->
            <div class="col-lg-3 col-md-4 col-sm-4">
                <div class="card card-stats">
                    <a href="{{ route('admin.qr') }}">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-5 col-md-4">
                                    <div class="icon-big text-center icon-warning">
                                        <i class="fas fa-qrcode text-success"></i>
                                    </div>
                                </div>
                                <div class="col-7 col-md-8">
                                    <div class="numbers">
                                        <p class="card-category">Total QR Codes</p>
                                        <p class="card-title">{{ $qrCount }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-md-4 col-sm-4">
                <div class="card card-stats">
                    <a href="{{ route('admin.qr') }}">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-5 col-md-4">
                                    <div class="icon-big text-center icon-warning">
                                        <i class="fas fa-qrcode text-primary"></i>
                                    </div>
                                </div>
                                <div class="col-7 col-md-8">
                                    <div class="numbers">
                                        <p class="card-category">Active QR Codes</p>
                                        <p class="card-title">{{ $activeQrCount }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-md-4 col-sm-4">
                <div class="card card-stats">
                    <a href="{{ route('admin.usedQr') }}">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-5 col-md-4">
                                    <div class="icon-big text-center icon-warning">
                                        <i class="fas fa-qrcode text-danger"></i>
                                    </div>
                                </div>
                                <div class="col-7 col-md-8">
                                    <div class="numbers">
                                        <p class="card-category">Used QR Codes</p>
                                        <p class="card-title">{{ $usedQrCount }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Sliders -->
            <div class="col-lg-3 col-md-4 col-sm-4">
                <div class="card card-stats">
                    <a href="{{ route('admin.sliders.index') }}">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-5 col-md-4">
                                    <div class="icon-big text-center icon-warning">
                                        <i class="fas fa-tachometer-alt text-warning"></i>
                                    </div>
                                </div>
                                <div class="col-7 col-md-8">
                                    <div class="numbers">
                                        <p class="card-category">Active <br> Sliders</p>
                                        <p class="card-title">{{ $sliderCount }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Redemption Requests -->
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="card card-stats">
                    <a href="{{ route('admin.redemption', ['status' => 'approved']) }}">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-5 col-md-4">
                                    <div class="icon-big text-center icon-warning">
                                        <i class="fas fa-check text-success"></i>
                                    </div>
                                </div>
                                <div class="col-7 col-md-8">
                                    <div class="numbers">
                                        <p class="card-category">Approved Requests</p>
                                        <p class="card-title">{{ $approvedCount }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="card card-stats">
                    <a href="{{ route('admin.redemption', ['status' => 'pending']) }}">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-5 col-md-4">
                                    <div class="icon-big text-center icon-warning">
                                        <i class="fas fa-clock text-warning"></i>
                                    </div>
                                </div>
                                <div class="col-7 col-md-8">
                                    <div class="numbers">
                                        <p class="card-category">Pending Requests</p>
                                        <p class="card-title">{{ $pendingCount }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            {{-- <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="card card-stats">
                    <a href="{{ route('admin.redemption', ['status' => 'completed']) }}">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-5 col-md-4">
                                    <div class="icon-big text-center icon-warning">
                                        <i class="fas fa-check-double text-primary"></i>
                                    </div>
                                </div>
                                <div class="col-7 col-md-8">
                                    <div class="numbers">
                                        <p class="card-category">Completed Requests</p>
                                        <p class="card-title">{{ $completedCount }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div> --}}

            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="card card-stats">
                    <a href="{{ route('admin.redemption', ['status' => 'rejected']) }}">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-5 col-md-4">
                                    <div class="icon-big text-center icon-warning">
                                        <i class="fas fa-times text-danger"></i>
                                    </div>
                                </div>
                                <div class="col-7 col-md-8">
                                    <div class="numbers">
                                        <p class="card-category">Rejected Requests</p>
                                        <p class="card-title">{{ $rejectedCount }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>



            <!-- Users -->
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="card card-stats">
                    <a href="{{ route('admin.users', ['status' => 'all']) }}">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-5 col-md-4">
                                    <div class="icon-big text-center icon-warning">
                                        <i class="fas fa-users text-primary"></i>
                                    </div>
                                </div>
                                <div class="col-7 col-md-8">
                                    <div class="numbers">
                                        <p class="card-category">Total<br> Users</p>
                                        <p class="card-title">{{ $totalUserCount }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="card card-stats">
                    <a href="{{ route('admin.users', ['status' => 'inactive']) }}">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-5 col-md-4">
                                    <div class="icon-big text-center icon-warning">
                                        <i class="fas fa-user-times text-danger"></i>
                                    </div>
                                </div>
                                <div class="col-7 col-md-8">
                                    <div class="numbers">
                                        <p class="card-category">Inactive<br> Users</p>
                                        <p class="card-title">{{ $inactiveUserCount }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="card card-stats">
                    <a href="{{ route('admin.users', ['status' => 'pending']) }}">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-5 col-md-4">
                                    <div class="icon-big text-center icon-warning">
                                        <i class="fas fa-user-clock text-warning"></i>
                                    </div>
                                </div>
                                <div class="col-7 col-md-8">
                                    <div class="numbers">
                                        <p class="card-category">Pending <br> Users</p>
                                        <p class="card-title">{{ $pendingUserCount }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="card card-stats">
                    <a href="{{ route('admin.users', ['status' => 'blocked']) }}">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-5 col-md-4">
                                    <div class="icon-big text-center icon-warning">
                                        <i class="fas fa-ban text-warning"></i>
                                    </div>
                                </div>
                                <div class="col-7 col-md-8">
                                    <div class="numbers">
                                        <p class="card-category">Blocked <br> Users</p>
                                        <p class="card-title">{{ $blockedUserCount }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

        </div>


        <div class="row">
            <div class="col-md-12">
                <div class="card ">
                    <div class="card-header ">
                        <h5 class="card-title">Users Behavior</h5>
                        <p class="card-category">24 Hours performance</p>
                    </div>
                    <div class="card-body ">
                        <canvas id=chartDays width="400" height="100"></canvas>
                    </div>
                    {{-- <div class="card-footer ">
                        <hr>
                        <div class="stats">
                            <i class="fa fa-history"></i> Updated 3 minutes ago
                        </div>
                    </div> --}}
                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Javascript method's body can be found in assets/assets-for-demo/js/demo.js
            demo.initChartsPages();
        });
    </script>
    <script>
        // Chart.js code
        const ctx = document.getElementById('chartDays').getContext('2d');
        const chartDays = new Chart(ctx, {
            type: 'line', // Choose the type of chart
            data: {
                labels: @json($dates), // Date labels
                datasets: [{
                    label: 'User Registrations',
                    data: @json($activityCounts), // Registration counts
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    fill: false
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Date'
                        },
                        ticks: {
                            callback: function(value) {
                                return new Date(value).toLocaleDateString(); // Format the date
                            }
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Number of Registrations'
                        }
                    }
                }
            }
        });
    </script>
@endpush
