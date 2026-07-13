@extends('admin.layouts.master')

@section('title', 'Pre-Register Details')

@section('main-content')
<section class="section">
    <div class="section-header">
        <h1>Pre-Register Details</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="{{ route('admin.pre-registers.index') }}">Pre-Registers</a></div>
            <div class="breadcrumb-item active">Details</div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Visitor Name</label>
                                    <p>{{ $preregister->visitor->name ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Email</label>
                                    <p>{{ $preregister->visitor->email ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Phone</label>
                                    <p>{{ $preregister->visitor->phone ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Status</label>
                                    <p><span class="badge badge-{{ $preregister->status == 'approved' ? 'success' : ($preregister->status == 'pending' ? 'warning' : 'danger') }}">
                                        {{ ucfirst($preregister->status ?? 'pending') }}
                                    </span></p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Host</label>
                                    <p>{{ $preregister->host_name }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Host Type</label>
                                    <p>{{ ucfirst($preregister->host_type) }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Facility</label>
                                    <p>{{ $preregister->facility->name ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Expected Date</label>
                                    <p>{{ $preregister->expected_date }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Expected Time</label>
                                    <p>{{ $preregister->expected_time ? date('h:i A', strtotime($preregister->expected_time)) : 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Purpose</label>
                                    <p>{{ $preregister->purpose ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">QR Code</label>
                                    <div>
                                        {!! QrCode::size(150)->generate($preregister->reference ?? $preregister->id) !!}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <a href="{{ route('admin.pre-registers.index') }}" class="btn btn-secondary">Back</a>
                            <a href="{{ route('admin.pre-registers.edit', $preregister->id) }}" class="btn btn-primary">Edit</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection