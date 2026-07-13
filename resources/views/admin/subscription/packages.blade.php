@extends('admin.layouts.master')

@section('title', 'Packages')

@section('main-content')
<section class="section">
    <div class="section-header">
        <h1>Subscriptions</h1>
        {{-- Breadcrumbs::render('packages') --}}
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Package List</h4>
                        <div class="card-header-action">
                            <a href="{{ route('admin.packages.create') }}" class="btn btn-primary">Add New Package</a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>MRP</th>
                                        <th>Amount</th>
                                        <th>Type</th>
                                        <th>Days</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($packages as $package)
                                        <tr>
                                            <td>{{ $package->id }}</td>
                                            <td>{{ $package->package_name }}</td>
                                            <td>{{ $package->mrp }}</td>
                                            <td>{{ $package->amount }}</td>
                                            <td>{{ ucfirst($package->subscription_type) }}</td>
                                            <td>{{ $package->days }}</td>
                                            <td>
                                                @if($package->status == 1)
                                                    <span class="badge badge-success">Active</span>
                                                @else
                                                    <span class="badge badge-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.packages.edit', $package->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                                <form action="{{ route('admin.packages.destroy', $package->id) }}" method="POST" style="display:inline-block;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                                </form>
                                                <!-- ✅ FIXED: Changed 'admin.packages.updateStatus' to 'admin.package.updateStatus' -->
                                                <form action="{{ route('admin.package.updateStatus') }}" method="POST" style="display:inline-block;">
                                                    @csrf
                                                    <input type="hidden" name="package_id" value="{{ $package->id }}">
                                                    <input type="hidden" name="status" value="{{ $package->status == 1 ? 0 : 1 }}">
                                                    <button type="submit" class="btn btn-sm {{ $package->status == 1 ? 'btn-warning' : 'btn-success' }}">
                                                        {{ $package->status == 1 ? 'Deactivate' : 'Activate' }}
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">No packages found.</td>
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