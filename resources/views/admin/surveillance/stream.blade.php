@extends('layouts.admin')

@section('title', 'Camera Stream - ' . $feed->name)

@section('content-header')
<div class="page-header">
    <h3 class="page-title">
        <span class="page-title-icon bg-gradient-primary text-white me-2">
            <i class="mdi mdi-eye"></i>
        </span>
        Camera Stream: {{ $feed->name }}
    </h3>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.surveillance.index') }}">Surveillance</a></li>
            <li class="breadcrumb-item active" aria-current="page">Stream</li>
        </ol>
    </nav>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h4 class="card-title">
                            {{ $feed->name }}
                            @if($feed->location)
                                <small class="text-muted">({{ $feed->location }})</small>
                            @endif
                        </h4>
                    </div>
                    <div class="col-md-6 text-end">
                        <span class="badge badge-{{ $feed->status === 'online' ? 'success' : ($feed->status === 'offline' ? 'danger' : ($feed->status === 'recording' ? 'primary' : 'warning')) }} badge-lg">
                            {{ $feed->status_label }}
                        </span>
                        @if($feed->is_recording)
                            <span class="badge badge-danger badge-lg">
                                <i class="mdi mdi-record"></i> Recording
                            </span>
                        @endif
                        <a href="{{ route('admin.surveillance.show', $feed->id) }}" class="btn btn-light btn-sm">
                            <i class="mdi mdi-arrow-left"></i> Back to Details
                        </a>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        @if($feed->status === 'online' || $feed->status === 'recording')
                            <div class="text-center">
                                <div class="video-container" style="position: relative; background: #000; border-radius: 8px; overflow: hidden;">
                                    @if($feed->stream_url)
                                        <video id="videoPlayer" controls autoplay style="width: 100%; max-height: 600px; background: #000;">
                                            <source src="{{ $feed->stream_url }}" type="video/mp4">
                                            <source src="{{ str_replace('.mp4', '.webm', $feed->stream_url) }}" type="video/webm">
                                            <source src="{{ str_replace('.mp4', '.ogg', $feed->stream_url) }}" type="video/ogg">
                                            Your browser does not support the video tag.
                                        </video>
                                    @else
                                        <div class="alert alert-warning" style="margin: 0;">
                                            <i class="mdi mdi-alert"></i>
                                            No stream URL configured for this camera.
                                            <a href="{{ route('admin.surveillance.edit', $feed->id) }}" class="alert-link">Configure stream URL</a>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="mt-3 text-center">
                                <button class="btn btn-outline-primary btn-sm" onclick="document.getElementById('videoPlayer').play()">
                                    <i class="mdi mdi-play"></i> Play
                                </button>
                                <button class="btn btn-outline-secondary btn-sm" onclick="document.getElementById('videoPlayer').pause()">
                                    <i class="mdi mdi-pause"></i> Pause
                                </button>
                                <button class="btn btn-outline-secondary btn-sm" onclick="document.getElementById('videoPlayer').requestFullscreen()">
                                    <i class="mdi mdi-fullscreen"></i> Fullscreen
                                </button>
                            </div>

                            <div class="mt-3">
                                <div class="alert alert-info">
                                    <i class="mdi mdi-information"></i>
                                    <strong>Camera Info:</strong>
                                    Type: {{ $feed->camera_type_label }} |
                                    Brand: {{ $feed->brand ?? 'N/A' }} |
                                    Model: {{ $feed->model ?? 'N/A' }} |
                                    IP: {{ $feed->ip_address ?? 'N/A' }}
                                    @if($feed->is_recording)
                                        | <span class="text-danger"><i class="mdi mdi-record"></i> Recording in progress</span>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <div class="alert alert-{{ $feed->status === 'error' ? 'danger' : 'warning' }}" style="padding: 60px 20px;">
                                    <i class="mdi mdi-{{ $feed->status === 'error' ? 'alert' : 'video-off' }}" style="font-size: 48px;"></i>
                                    <h4 class="mt-3">Camera is {{ $feed->status }}</h4>
                                    <p>The camera is currently {{ $feed->status }}. Please check the connection or configuration.</p>
                                    <div class="mt-3">
                                        <a href="{{ route('admin.surveillance.edit', $feed->id) }}" class="btn btn-primary btn-sm">
                                            <i class="mdi mdi-pencil"></i> Edit Configuration
                                        </a>
                                        <form action="{{ route('admin.surveillance.test-connection', $feed->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-warning btn-sm">
                                                <i class="mdi mdi-wifi"></i> Test Connection
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-play video when page loads
    var video = document.getElementById('videoPlayer');
    if (video) {
        video.play().catch(function(error) {
            console.log('Auto-play was prevented:', error);
        });
    }

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        if (e.key === ' ' || e.key === 'Spacebar') {
            e.preventDefault();
            var video = document.getElementById('videoPlayer');
            if (video) {
                if (video.paused) {
                    video.play();
                } else {
                    video.pause();
                }
            }
        }
        if (e.key === 'f' || e.key === 'F') {
            var video = document.getElementById('videoPlayer');
            if (video) {
                if (video.requestFullscreen) {
                    video.requestFullscreen();
                }
            }
        }
    });
});
</script>
@endpush