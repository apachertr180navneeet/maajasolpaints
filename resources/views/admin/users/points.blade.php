@extends('layouts.app', [

    'class' => '',

    'activeSection' => 'user-points',

])



@section('content')

    <div class="container">

        <div class="card shadow">

            <div class="card-header">

                <h4>{{ $title }}</h4>

            </div>

            <div class="card-body">

                <div class="table-responsive">

                    <table class="table align-items-center table-flush" id="datatable">

                        <thead class="thead-light">

                            <tr>

                                <th>Date</th>

                                <th>Username</th>

                                <th>Type</th>

                                <th>Total Points</th>

                                <th>Points Earned</th>

                                <th>Scheme Name</th>

                                <th>Expiry Date</th>

                                <th>Remaining Points</th>

                            </tr>

                        </thead>

                        <tbody>

                            @foreach ($transactions as $transaction)
                                
                                <tr>

                                    <td>{{ \Carbon\Carbon::parse($transaction->latest_date)->format('d/m/Y') }}</td>

                                    <td>{{ $transaction->user->name ?? 'N/A' }}</td>

                                    <td>

                                        <span class="badge bg-info">Product</span>

                                    </td>

                                    <td style="color: rgb(0, 255, 55);font-weight: bolder;">{{ $transaction->product->points ?? '0' }} PTS</td>

                                    <td style="font-weight: bolder;">{{ number_format($transaction->points_earned ?? 0) }} PTS</td>

                                    <td>{{ $transaction->product->name ?? 'N/A' }}</td>

                                    <td>{{ optional($transaction->product)->expiry_date ? $transaction->product->expiry_date->format('d/m/Y') : 'N/A' }}</td>

                                    </td>

                                    <td style="color: red;font-weight: bolder;">{{ number_format(abs(optional($transaction->product)->points - $transaction->points_earned ?? 0)) }}PTS</td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

@endsection

