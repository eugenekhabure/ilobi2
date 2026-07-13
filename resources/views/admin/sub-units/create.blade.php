@extends('admin.layouts.master')

@section('title', 'Create Sub Unit')

@section('main-content')
<section class="section">
    <div class="section-header">
        <h1>Create Sub Unit</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="{{ route('admin.sub-units.index') }}">Sub Units</a></div>
            <div class="breadcrumb-item active">Create</div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.sub-units.store') }}">
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
                                <label>Parent Sub Unit</label>
                                <select name="parent_id" class="form-control">
                                    <option value="">None (Top Level)</option>
                                    @foreach($parentUnits as $unit)
                                        <option value="{{ $unit->id }}" {{ old('parent_id') == $unit->id ? 'selected' : '' }}>
                                            {{ $unit->name }} ({{ ucfirst($unit->type) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Type <span class="text-danger">*</span></label>
                                <select name="type" class="form-control @error('type') is-invalid @enderror" required>
                                    <option value="">Select Type</option>
                                    <option value="block" {{ old('type') == 'block' ? 'selected' : '' }}>Block</option>
                                    <option value="floor" {{ old('type') == 'floor' ? 'selected' : '' }}>Floor</option>
                                    <option value="wing" {{ old('type') == 'wing' ? 'selected' : '' }}>Wing</option>
                                    <option value="tower" {{ old('type') == 'tower' ? 'selected' : '' }}>Tower</option>
                                    <option value="street" {{ old('type') == 'street' ? 'selected' : '' }}>Street</option>
                                    <option value="unit" {{ old('type') == 'unit' ? 'selected' : '' }}>Unit</option>
                                    <option value="apartment" {{ old('type') == 'apartment' ? 'selected' : '' }}>Apartment</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required placeholder="e.g. Block A, Apartment 101">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Description</label>
                                <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Create Sub Unit</button>
                                <a href="{{ route('admin.sub-units.index') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection