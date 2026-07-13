@extends('admin.layouts.master')

@section('title', 'Edit Resident Profile')

@section('main-content')
<section class="section">
    <div class="section-header">
        <h1>Edit Resident Profile</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="{{ route('admin.resident-profiles.index') }}">Resident Profiles</a></div>
            <div class="breadcrumb-item active">Edit</div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.resident-profiles.update', $residentProfile->id) }}">
                            @csrf
                            @method('PUT')

                            <div class="form-group">
                                <label>Person <span class="text-danger">*</span></label>
                                <select name="person_id" class="form-control @error('person_id') is-invalid @enderror" required>
                                    <option value="">Select Person</option>
                                    @foreach($people as $person)
                                        <option value="{{ $person->id }}" {{ old('person_id', $residentProfile->person_id) == $person->id ? 'selected' : '' }}>
                                            {{ $person->first_name }} {{ $person->last_name }} ({{ $person->email ?? 'No email' }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('person_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Sub Unit (Apartment/Unit) <span class="text-danger">*</span></label>
                                <select name="sub_unit_id" class="form-control @error('sub_unit_id') is-invalid @enderror" required>
                                    <option value="">Select Sub Unit</option>
                                    @foreach($subUnits as $unit)
                                        <option value="{{ $unit->id }}" {{ old('sub_unit_id', $residentProfile->sub_unit_id) == $unit->id ? 'selected' : '' }}>
                                            {{ $unit->name }} ({{ ucfirst($unit->type) }}) - {{ $unit->facility->name ?? 'N/A' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('sub_unit_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Lease Start</label>
                                <input type="date" name="lease_start" class="form-control @error('lease_start') is-invalid @enderror" value="{{ old('lease_start', $residentProfile->lease_start) }}">
                                @error('lease_start')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Lease End</label>
                                <input type="date" name="lease_end" class="form-control @error('lease_end') is-invalid @enderror" value="{{ old('lease_end', $residentProfile->lease_end) }}">
                                @error('lease_end')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Is Owner?</label>
                                <select name="is_owner" class="form-control">
                                    <option value="0" {{ old('is_owner', $residentProfile->is_owner) == '0' ? 'selected' : '' }}>No (Tenant)</option>
                                    <option value="1" {{ old('is_owner', $residentProfile->is_owner) == '1' ? 'selected' : '' }}>Yes (Owner)</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Update Resident Profile</button>
                                <a href="{{ route('admin.resident-profiles.index') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection