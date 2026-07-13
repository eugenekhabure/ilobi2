@extends('admin.layouts.master')

@section('title', 'Add Amenity')

@section('main-content')
<section class="section">
    <div class="section-header">
        <h1>🏊 Add Amenity</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="{{ route('admin.amenities.index') }}">Amenities</a></div>
            <div class="breadcrumb-item active">Add</div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('admin.amenities.store') }}">
                            @csrf

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Name <span class="text-danger">*</span></label>
                                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Icon</label>
                                        <select name="icon" class="form-control @error('icon') is-invalid @enderror">
                                            <option value="">None</option>
                                            @foreach($icons as $value => $label)
                                                <option value="{{ $value }}" {{ old('icon') == $value ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('icon')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Description</label>
                                <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Location</label>
                                        <input type="text" name="location" class="form-control @error('location') is-invalid @enderror" value="{{ old('location') }}" placeholder="e.g. Ground Floor">
                                        @error('location')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Capacity</label>
                                        <input type="number" name="capacity" class="form-control @error('capacity') is-invalid @enderror" value="{{ old('capacity', 10) }}">
                                        @error('capacity')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Max Booking Days</label>
                                        <input type="number" name="max_booking_days" class="form-control @error('max_booking_days') is-invalid @enderror" value="{{ old('max_booking_days', 7) }}">
                                        @error('max_booking_days')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Advance Notice (Hours)</label>
                                        <input type="number" name="advance_notice_hours" class="form-control @error('advance_notice_hours') is-invalid @enderror" value="{{ old('advance_notice_hours', 2) }}">
                                        @error('advance_notice_hours')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Price (KES)</label>
                                        <input type="number" step="0.01" name="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price', 0) }}">
                                        @error('price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>
                                            <input type="checkbox" name="requires_approval" value="1" checked>
                                            Requires Approval
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>
                                            <input type="checkbox" name="is_active" value="1" checked>
                                            Active
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <h5>Time Slots</h5>
                            <div id="time-slots-container">
                                <div class="time-slot-row row g-2 mb-2">
                                    <div class="col-md-3">
                                        <select name="time_slots[0][day_of_week]" class="form-control">
                                            <option value="">Select Day</option>
                                            @foreach($days as $value => $label)
                                                <option value="{{ $value }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="time" name="time_slots[0][start_time]" class="form-control" placeholder="Start">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="time" name="time_slots[0][end_time]" class="form-control" placeholder="End">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" name="time_slots[0][duration_minutes]" class="form-control" placeholder="Duration (min)" value="60">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" name="time_slots[0][max_bookings]" class="form-control" placeholder="Max Bookings" value="1">
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-danger remove-slot">×</button>
                                    </div>
                                </div>
                            </div>

                            <button type="button" class="btn btn-secondary btn-sm mb-3" onclick="addTimeSlot()">
                                <i class="fas fa-plus"></i> Add Time Slot
                            </button>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Save Amenity
                                </button>
                                <a href="{{ route('admin.amenities.index') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    let slotIndex = 1;

    function addTimeSlot() {
        const container = document.getElementById('time-slots-container');
        const newRow = document.createElement('div');
        newRow.className = 'time-slot-row row g-2 mb-2';
        newRow.innerHTML = `
            <div class="col-md-3">
                <select name="time_slots[${slotIndex}][day_of_week]" class="form-control">
                    <option value="">Select Day</option>
                    @foreach($days as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <input type="time" name="time_slots[${slotIndex}][start_time]" class="form-control" placeholder="Start">
            </div>
            <div class="col-md-2">
                <input type="time" name="time_slots[${slotIndex}][end_time]" class="form-control" placeholder="End">
            </div>
            <div class="col-md-2">
                <input type="number" name="time_slots[${slotIndex}][duration_minutes]" class="form-control" placeholder="Duration (min)" value="60">
            </div>
            <div class="col-md-2">
                <input type="number" name="time_slots[${slotIndex}][max_bookings]" class="form-control" placeholder="Max Bookings" value="1">
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-danger remove-slot" onclick="removeSlot(this)">×</button>
            </div>
        `;
        container.appendChild(newRow);
        slotIndex++;
    }

    function removeSlot(button) {
        const row = button.closest('.time-slot-row');
        if (document.querySelectorAll('.time-slot-row').length > 1) {
            row.remove();
        } else {
            alert('You need at least one time slot.');
        }
    }

    // Remove slot event delegation
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-slot')) {
            const row = e.target.closest('.time-slot-row');
            if (document.querySelectorAll('.time-slot-row').length > 1) {
                row.remove();
            } else {
                alert('You need at least one time slot.');
            }
        }
    });
</script>
@endsection