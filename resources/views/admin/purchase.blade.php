@extends('admin.layouts.master')

@section('title', 'Purchase Package')

@section('main-content')
<section class="section">
    <div class="section-header">
        <h1>Purchase Package</h1>
        {{-- Breadcrumbs::render('purchase') --}}   <!-- Commented out breadcrumb -->
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        @if(session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        <div class="row">
                            @if(isset($packages) && count($packages) > 0)
                                @foreach($packages as $package)
                                    <div class="col-md-4 mb-4">
                                        <div class="card h-100">
                                            <div class="card-body text-center">
                                                <h4 class="card-title">{{ $package->package_name }}</h4>
                                                <p class="card-text">
                                                    <strong>Price:</strong> KES {{ number_format($package->amount, 2) }}<br>
                                                    <strong>Type:</strong> {{ ucfirst($package->subscription_type) }}<br>
                                                    <strong>Duration:</strong> {{ $package->days }} days
                                                </p>
                                                <form method="POST" action="{{ route('subscription.purchase_store') }}" enctype="multipart/form-data">
                                                    @csrf
                                                    <input type="hidden" name="package_id" value="{{ $package->id }}">
                                                    <div class="form-group">
                                                        <label>Transaction Number</label>
                                                        <input type="text" name="transaction_number" class="form-control" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Transaction Image</label>
                                                        <input type="file" name="transaction_image" class="form-control">
                                                    </div>
                                                    <button type="submit" class="btn btn-primary btn-block">Purchase</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="col-12 text-center">
                                    <p>No packages available for purchase.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection