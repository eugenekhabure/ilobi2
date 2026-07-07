@extends('admin.layouts.master')

@section('main-content')
<section class="section">
    <div class="section-header">
        <h1>Subscriptions</h1>
    </div>

    <div class="section-body">
        <div class="row">
            @foreach ($packages as $package)
                <div class="col-md-3 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <h5 class="card-title">{{ $package->name }}</h5>
                            <p class="text-muted">MRP: <del>${{ $package->mrp }}</del></p>
                            <h4 class="text-success">${{ $package->amount }}</h4>
                            <p>Valid for <strong>{{ $package->days }}</strong> days</p>
                            <p>Type: <strong>{{ $package->type }}</strong></p>
                            <a href="#" class="btn btn-primary" 
                               data-bs-toggle="modal" 
                               data-bs-target="#purchaseModal" 
                               data-id="{{ $package->id }}" 
                               data-name="{{ $package->name }}" 
                               data-amount="{{ $package->amount }}" 
                               data-days="{{ $package->days }}">
                               Buy Now
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Purchase Modal -->
<div class="modal fade" id="purchaseModal" tabindex="-1" aria-labelledby="purchaseModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-center" id="purchaseModalLabel">Purchase Details</h5>
            </div>
            <div class="modal-body">
                <form action="{{ route('subscription.purchase_store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="package_id" id="package_id" value="{{ old('package_id') }}">
                    
                    <div class="mb-3">
                        <label class="form-label">Package Name</label>
                        <input type="text" class="form-control" id="package_name" value="{{ old('package_name') }}" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Days</label>
                        <input type="text" class="form-control" id="days" name="days" value="{{ old('days') }}" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Amount</label>
                        <input type="text" class="form-control" id="package_amount" name="package_amount" value="{{ old('package_amount') }}" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Transaction Number</label>
                        <input type="text" class="form-control" name="transaction_number" value="{{ old('transaction_number') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Upload Payment Screenshot</label>
                        <input type="file" class="form-control" name="transaction_image" accept="image/*" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Bootstrap + Toastr --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
   document.addEventListener("DOMContentLoaded", function() {
        var purchaseModal = document.getElementById('purchaseModal');
        purchaseModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            if (!button) return;

            var packageId = button.getAttribute('data-id');
            var packageName = button.getAttribute('data-name');
            var packageAmount = button.getAttribute('data-amount');
            var days = button.getAttribute('data-days');
            
            // Set values
            document.getElementById('package_id').value = packageId;
            document.getElementById('package_name').value = packageName;
            document.getElementById('days').value = days;
            document.getElementById('package_amount').value = packageAmount;
        });

        // ✅ Toastr Notifications
        @if ($errors->any())
            @foreach ($errors->all() as $error)
                toastr.error("{{ $error }}");
            @endforeach
            // reopen modal so user can see form again
            var myModal = new bootstrap.Modal(document.getElementById('purchaseModal'));
            myModal.show();
        @endif

        @if (session('error'))
            toastr.error("{{ session('error') }}");
        @endif

        @if (session('success'))
            toastr.success("{{ session('success') }}");
        @endif
   });
</script>

@endsection
