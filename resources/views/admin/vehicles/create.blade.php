@extends('admin.layouts.master')

@section('title', 'Create Vehicle')

@section('main-content')
<section class="section">
    <div class="section-header">
        <h1>Create Vehicle</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="{{ route('admin.vehicles.index') }}">Vehicles</a></div>
            <div class="breadcrumb-item active">Create</div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.vehicles.store') }}">
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
                                <label>Plate Number <span class="text-danger">*</span></label>
                                <input type="text" name="plate_number" class="form-control @error('plate_number') is-invalid @enderror" value="{{ old('plate_number') }}" required placeholder="e.g. KCA 123X">
                                @error('plate_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Make</label>
                                <input type="text" name="make" class="form-control @error('make') is-invalid @enderror" value="{{ old('make') }}" placeholder="e.g. Toyota">
                                @error('make')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Model</label>
                                <input type="text" name="model" class="form-control @error('model') is-invalid @enderror" value="{{ old('model') }}" placeholder="e.g. Hilux">
                                @error('model')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Color</label>
                                <input type="text" name="color" class="form-control @error('color') is-invalid @enderror" value="{{ old('color') }}" placeholder="e.g. White">
                                @error('color')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Owner Type <span class="text-danger">*</span></label>
                                <select name="owner_type" class="form-control @error('owner_type') is-invalid @enderror" required>
                                    <option value="">Select Owner Type</option>
                                    <option value="App\Models\Person" {{ old('owner_type') == 'App\Models\Person' ? 'selected' : '' }}>Person</option>
                                    <option value="App\Models\Visitor" {{ old('owner_type') == 'App\Models\Visitor' ? 'selected' : '' }}>Visitor</option>
                                </select>
                                @error('owner_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Owner <span class="text-danger">*</span></label>
                                <select name="owner_id" class="form-control @error('owner_id') is-invalid @enderror" required>
                                    <option value="">Select Owner</option>
                                    @foreach($people as $person)
                                        <option value="{{ $person->id }}" {{ old('owner_id') == $person->id ? 'selected' : '' }}>
                                            {{ $person->first_name }} {{ $person->last_name }} ({{ $person->email ?? 'No email' }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('owner_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Create Vehicle</button>
                                <a href="{{ route('admin.vehicles.index') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection