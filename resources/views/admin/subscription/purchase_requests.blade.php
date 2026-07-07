@extends('admin.layouts.master')

@section('main-content')


<section class="section">
    <div class="section-header">
        <h1>Package Purchase Requests</h1>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">

                        @if(session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>User Id</th>
                                        <th>Transaction Number</th>
                                        <th>Status</th>
                                        <th>Amount</th>
                                        <th>Approval Date</th>
                                        <th>Transaction Image</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($data as $package)
                                        <tr id="row-{{ $package->id }}">
                                            <td>{{ $package->id }}</td>
                                            <td>{{ $package->userid }}</td>
                                            <td>{{ $package->transaction_number }}</td>
                                            <td id="status-{{ $package->id }}">{{ ucfirst($package->status) }}</td>
                                            <td>{{ $package->package_amount }}</td>
                                            <td>{{ $package->admin_approved }}</td>
                                            <td>
                                                @if($package->transaction_image)
                                                    <a href="{{ asset('storage/transactions/' . basename($package->transaction_image)) }}" target="_blank">
                                                        <img src="{{ asset('storage/transactions/' . basename($package->transaction_image)) }}" width="50" height="50" class="img-thumbnail">
                                                    </a>
                                                @else
                                                    No Image
                                                @endif
                                            </td>
                                            <td>
                                                @if($package->status == 'pending')
                                                    <form action="{{ route('admin.package.updateStatus') }}" method="POST" class="d-inline update-status-form">
                                                        @csrf
                                                        <input type="hidden" name="id" value="{{ $package->id }}">
                                                        <input type="hidden" name="status" value="approved">
                                                        <button type="submit" class="btn btn-success btn-sm">Approve</button>
                                                    </form>

                                                    <form action="{{ route('admin.package.updateStatus') }}" method="POST" class="d-inline update-status-form">
                                                        @csrf
                                                        <input type="hidden" name="id" value="{{ $package->id }}">
                                                        <input type="hidden" name="status" value="rejected">
                                                        <button type="submit" class="btn btn-danger btn-sm">Reject</button>
                                                    </form>
                                                @else
                                                    <span class="badge text-white bg-{{ $package->status == 'approved' ? 'success' : 'danger' }}">
                                                        {{ ucfirst($package->status) }}
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    @if($data->isEmpty())
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
<!-- Modal -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statusModalLabel">Confirm Status Change</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="confirmationMessage">Are you sure you want to change the status?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmStatusChange">Yes, Proceed</button>
            </div>
        </div>
    </div>
</div>

@endsection
@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        let formToSubmit = null;
        let statusToChange = null;
        
        document.querySelectorAll('.update-status-form').forEach(form => {
            form.addEventListener('submit', function(event) {
                event.preventDefault(); 
                
                formToSubmit = form;
                statusToChange = this.querySelector('[name="status"]').value;
                
                let message = statusToChange === "approved" 
                    ? "Are you sure you want to approve this package?" 
                    : "Are you sure you want to reject this package?";

                document.getElementById('confirmationMessage').textContent = message;
                
                $('#statusModal').modal('show');
            });
        });

        document.getElementById('confirmStatusChange').addEventListener('click', function() {
            formToSubmit.submit();
        });
    });
</script>
@endsection

