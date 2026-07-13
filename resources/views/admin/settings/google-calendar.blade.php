@extends('admin.layouts.master')

@section('title', 'Google Calendar Settings')

@section('main-content')
<section class="section">
    <div class="section-header">
        <h1>📅 Google Calendar Settings</h1>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Connect Google Calendar</h4>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        @if(session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        <div class="row">
                            <div class="col-md-8">
                                @if($isConnected)
                                    <div class="alert alert-success">
                                        <i class="fas fa-check-circle me-2"></i>
                                        <strong>Connected!</strong> Your Google Calendar is connected and ready to sync.
                                    </div>

                                    <div class="mt-3">
                                        <h6>Sync Settings</h6>
                                        <form method="POST" action="{{ route('admin.google-calendar.disconnect') }}">
                                            @csrf
                                            <button type="submit" class="btn btn-danger">
                                                <i class="fas fa-unlink me-2"></i>Disconnect Google Calendar
                                            </button>
                                        </form>
                                    </div>

                                    @if($primaryCalendar)
                                        <div class="mt-3">
                                            <h6>Primary Calendar</h6>
                                            <p><strong>Name:</strong> {{ $primaryCalendar->getSummary() }}</p>
                                            <p><strong>Email:</strong> {{ $primaryCalendar->getId() }}</p>
                                        </div>
                                    @endif
                                @else
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <strong>Not Connected!</strong> Connect your Google Calendar to automatically sync visitor appointments.
                                    </div>

                                    <p class="mt-3">Benefits of connecting Google Calendar:</p>
                                    <ul>
                                        <li>✅ Automatically create calendar events for visitor appointments</li>
                                        <li>✅ Send calendar invites to hosts and visitors</li>
                                        <li>✅ Receive reminders before visitor arrivals</li>
                                        <li>✅ Sync visitor schedules across all devices</li>
                                    </ul>

                                    <a href="{{ $authUrl }}" class="btn btn-primary mt-3">
                                        <i class="fab fa-google me-2"></i>Connect with Google
                                    </a>
                                @endif
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <i class="fab fa-google fa-4x text-primary mb-3"></i>
                                        <h6>Google Calendar</h6>
                                        <p class="text-muted small">Sync your visitor appointments with Google Calendar</p>
                                        <span class="badge badge-{{ $isConnected ? 'success' : 'danger' }}">
                                            {{ $isConnected ? 'Connected' : 'Not Connected' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sync History --}}
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Recent Calendar Syncs</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Visitor</th>
                                        <th>Event ID</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentSyncs ?? [] as $sync)
                                        <tr>
                                            <td>{{ $sync->visitor->name ?? 'N/A' }}</td>
                                            <td>{{ $sync->google_event_id ?? 'N/A' }}</td>
                                            <td>{{ $sync->created_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                @if($sync->google_event_link)
                                                    <a href="{{ $sync->google_event_link }}" target="_blank" class="btn btn-sm btn-info">
                                                        <i class="fab fa-google"></i> View
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="text-center">No calendar syncs yet.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection