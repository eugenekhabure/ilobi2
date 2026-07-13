@extends('admin.layouts.master')

@section('title', 'Purchase Requests')

@section('main-content')
<section class="section">
    <div class="section-header">
        <h1>Package Purchase Requests</h1>
        {{-- Breadcrumbs::render('purchase_requests') --}}   <!-- Commented out breadcrumb -->
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>User</th>
                                        <th>Package</th>
                                        <th>Transaction Number</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($requests as $request)
                                        <tr>
                                            <td>{{ $request->id }}</td>
                                            <td>{{ $request->user_name }}</td>
                                            <td>{{ $request->package_name }}</td>
                                            <td>{{ $request->transaction_number }}</td>
                                            <td>KES {{ number_format($request->amount, 2) }}</td>
                                            <td>
                                                <span class="badge badge-{{ $request->status == 'approved' ? 'success' : ($request->status == 'pending' ? 'warning' : 'danger') }}">
                                                    {{ ucfirst($request->status) }}
                                                </span>
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($request->created_at)->format('Y-m-d H:i') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">No package requests found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection