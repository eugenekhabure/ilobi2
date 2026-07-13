@extends('admin.layouts.master')

@section('title', 'Create Invitation')

@section('main-content')
<section class="section">
    <div class="section-header">
        <h1>Create Invitation</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="{{ route('admin.invitations.index') }}">Invitations</a></div>
            <div class="breadcrumb-item active">Create</div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.invitations.store') }}">
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
                                <label>Host <span class="text-danger">*</span></label>
                                <select name="host_person_id" class="form-control @error('host_person_id') is-invalid @enderror" required>
                                    <option value="">Select Host</option>
                                    @foreach($people as $person)
                                        <option value="{{ $person->id }}" {{ old('host_person_id') == $person->id ? 'selected' : '' }}>
                                            {{ $person->first_name }} {{ $person->last_name }} ({{ $person->email ?? 'No email' }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('host_person_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Visitor Email</label>
                                <input type="email" name="visitor_email" class="form-control @error('visitor_email') is-invalid @enderror" value="{{ old('visitor_email') }}" placeholder="guest@email.com">
                                @error('visitor_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Visitor Phone</label>
                                <input type="text" name="visitor_phone" class="form-control @error('visitor_phone') is-invalid @enderror" value="{{ old('visitor_phone') }}" placeholder="+254 700 000 000">
                                @error('visitor_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Sub Unit (Destination)</label>
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
                                <label>Expires At</label>
                                <input type="datetime-local" name="expires_at" class="form-control @error('expires_at') is-invalid @enderror" value="{{ old('expires_at', now()->addDays(1)->format('Y-m-d\TH:i')) }}">
                                @error('expires_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Create Invitation</button>
                                <a href="{{ route('admin.invitations.index') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection