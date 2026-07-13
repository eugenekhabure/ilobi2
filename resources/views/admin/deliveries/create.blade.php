@extends('admin.layouts.master')

@section('title', 'Create Delivery')

@section('main-content')
<section class="section">
    <div class="section-header">
        <h1>Create Delivery</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="{{ route('admin.deliveries.index') }}">Deliveries</a></div>
            <div class="breadcrumb-item active">Create</div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.deliveries.store') }}">
                            @csrf

                            <div class="form-group">
                                <label>Facility <span class="text-danger">*</span></label>
                                <select name="facility_id" class="form-control @error('facility_id') is-invalid @enderror" required>
                                    <option value="">Select Facility</option>
                                    @foreach($facilities as $facility)
                                        <option value="{{ $facility->id }}" {{ old('facility_id') == $facility->id ? 'selected' : '' }}>
                                            {{ $facility->name }} ({{ ucfirst($facility->type) }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('facility_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Courier Name</label>
                                <input type="text" name="courier_name" class="form-control @error('courier_name') is-invalid @enderror" value="{{ old('courier_name') }}" placeholder="e.g. DHL">
                                @error('courier_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Tracking Number</label>
                                <input type="text" name="tracking_number" class="form-control @error('tracking_number') is-invalid @enderror" value="{{ old('tracking_number') }}">
                                @error('tracking_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Recipient <span class="text-danger">*</span></label>
                                <select name="recipient_person_id" class="form-control @error('recipient_person_id') is-invalid @enderror" required>
                                    <option value="">Select Recipient</option>
                                    @foreach($people as $person)
                                        <option value="{{ $person->id }}" {{ old('recipient_person_id') == $person->id ? 'selected' : '' }}>
                                            {{ $person->first_name }} {{ $person->last_name }} ({{ $person->email ?? 'No email' }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('recipient_person_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Sub Unit (Delivery Location)</label>
                                <select name="sub_unit_id" class="form-control">
                                    <option value="">Select Sub Unit</option>
                                    @foreach($subUnits as $unit)
                                        <option value="{{ $unit->id }}" {{ old('sub_unit_id') == $unit->id ? 'selected' : '' }}>
                                            {{ $unit->name }} ({{ ucfirst($unit->type) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-control @error('status') is-invalid @enderror" required>
                                    <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="received" {{ old('status') == 'received' ? 'selected' : '' }}>Received</option>
                                    <option value="rejected" {{ old('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Notes</label>
                                <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Create Delivery</button>
                                <a href="{{ route('admin.deliveries.index') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection