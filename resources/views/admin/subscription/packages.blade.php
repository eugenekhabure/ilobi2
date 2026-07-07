@extends('admin.layouts.master')

@section('main-content')
<section class="section">
    <div class="section-header">
        <h1>Subscriptions</h1>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createPackageModal">
                            <i class="fas fa-plus"></i> Create Package
                        </button>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        @if(session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
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
                                    @foreach($packages as $package)
                                        <tr>
                                            <td>{{ $package->id }}</td>
                                            <td>{{ $package->name }}</td>
                                            <td>{{ $package->mrp }}</td>
                                            <td>{{ $package->amount }}</td>
                                            <td>{{ ucfirst($package->type) }}</td>
                                            <td>{{ $package->days }}</td>
                                            <td>
                                                @if($package->status == 1)
                                                    <span class="badge badge-success">Active</span>
                                                @else
                                                    <span class="badge badge-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <button class="btn btn-warning edit-btn" 
                                                    data-id="{{ $package->id }}" 
                                                    data-name="{{ $package->name }}" 
                                                    data-mrp="{{ $package->mrp }}" 
                                                    data-amount="{{ $package->amount }}" 
                                                    data-type="{{ $package->type }}" 
                                                    data-days="{{ $package->days }}" 
                                                    data-status="{{ $package->status }}"
                                                    data-toggle="modal" data-target="#editPackageModal">
                                                    <i class="fas fa-edit"></i> Edit
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                    @if($packages->isEmpty())
                                        <tr>
                                            <td colspan="8" class="text-center">No packages found</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Create Package Modal -->
<div class="modal fade" id="createPackageModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create Package</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="createPackageForm" method="POST" action="{{ route('admin.packages.store') }}">
                    @csrf
                    <div class="form-group">
                        <label>Package Name</label>
                        <input type="text" class="form-control" name="package_name" required>
                    </div>
                    <div class="form-group">
                        <label>MRP</label>
                        <input type="number" class="form-control" name="mrp" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label>Amount</label>
                        <input type="number" class="form-control" name="amount" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label>Subscription Type</label>
                        <select class="form-control" name="subscription_type" required>
                            <option value="daily">Daily</option>
                            <option value="monthly">Monthly</option>
                            <option value="yearly">Yearly</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Days</label>
                        <input type="number" class="form-control" name="enter_days" required>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select class="form-control" name="status" required>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Package</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Package Modal -->
<div class="modal fade" id="editPackageModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Package</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editPackageForm" method="POST" action="{{ route('packages.update') }}">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" id="edit-package-id">
                    <div class="form-group">
                        <label>Package Name</label>
                        <input type="text" class="form-control" name="package_name" id="edit-package-name" required>
                    </div>
                    <div class="form-group">
                        <label>MRP</label>
                        <input type="number" class="form-control" name="mrp" id="edit-package-mrp" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label>Amount</label>
                        <input type="number" class="form-control" name="amount" id="edit-package-amount" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label>Subscription Type</label>
                        <select class="form-control" name="subscription_type" id="edit-package-type" required>
                            <option value="daily">Daily</option>
                            <option value="monthly">Monthly</option>
                            <option value="yearly">Yearly</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Days</label>
                        <input type="number" class="form-control" name="enter_days" id="edit-package-days" required>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select class="form-control" name="status" id="edit-package-status" required>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Package</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).ready(function() {
        $('.edit-btn').click(function() {
            $('#edit-package-id').val($(this).data('id'));
            $('#edit-package-name').val($(this).data('name'));
            $('#edit-package-mrp').val($(this).data('mrp'));
            $('#edit-package-amount').val($(this).data('amount'));
            $('#edit-package-type').val($(this).data('type').toLowerCase());
            $('#edit-package-days').val($(this).data('days'));
            $('#edit-package-status').val($(this).data('status'));
        });
    });
</script>
@endsection
