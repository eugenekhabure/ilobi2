@extends('admin.layouts.master')

@section('title', 'Send Broadcast')

@section('main-content')
<section class="section">
    <div class="section-header">
        <h1>📢 Send Broadcast</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="{{ route('admin.broadcasts.index') }}">Broadcasts</a></div>
            <div class="breadcrumb-item active">Send</div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Send New Broadcast</h4>
                    </div>
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

                        @if(isset($template))
                            <div class="alert alert-info">
                                <i class="fas fa-file-alt me-2"></i>
                                Using template: <strong>{{ $template->name }}</strong>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('admin.broadcasts.store') }}">
                            @csrf

                            <div class="form-group">
                                <label>Channel <span class="text-danger">*</span></label>
                                <select name="channel" class="form-control @error('channel') is-invalid @enderror" required>
                                    <option value="">Select Channel</option>
                                    @foreach($channelOptions as $value => $label)
                                        <option value="{{ $value }}" {{ old('channel', $template->channel ?? '') == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('channel')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $template->subject ?? '') }}" placeholder="e.g. Estate Announcement" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Message <span class="text-danger">*</span></label>
                                <textarea name="message" class="form-control @error('message') is-invalid @enderror" rows="6" placeholder="Type your message..." required>{{ old('message', $template->message ?? '') }}</textarea>
                                @error('message')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Target Audience <span class="text-danger">*</span></label>
                                <select name="target_groups[]" class="form-control @error('target_groups') is-invalid @enderror" multiple required>
                                    @foreach($groupOptions as $value => $label)
                                        <option value="{{ $value }}" {{ in_array($value, old('target_groups', $template->target_groups ?? [])) ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Hold Ctrl/Cmd to select multiple audiences.</small>
                                @error('target_groups')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Schedule (Optional)</label>
                                <input type="datetime-local" name="scheduled_at" class="form-control @error('scheduled_at') is-invalid @enderror" value="{{ old('scheduled_at') }}">
                                @error('scheduled_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Leave blank to send immediately.</small>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane me-2"></i>Send Broadcast
                                </button>
                                <a href="{{ route('admin.broadcasts.index') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection