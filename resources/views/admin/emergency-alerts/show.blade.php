@extends('admin.layouts.master')

@section('title', 'Alert Details')

@section('main-content')
<section class="section">
    <div class="section-header">
        <h1>Alert Details</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="{{ route('admin.emergency-alerts.index') }}">Emergency Alerts</a></div>
            <div class="breadcrumb-item active">Details</div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4>{{ $alert->title }}</h4>
                        <div class="card-header-action">
                            <span class="badge badge-{{ $alert->severity_color }} badge-lg">
                                <i class="{{ $alert->severity_icon }} me-1"></i>
                                {{ strtoupper($alert->severity) }}
                            </span>
                            <span class="badge badge-{{ $alert->status == 'sent' ? 'success' : ($alert->status == 'failed' ? 'danger' : 'warning') }} badge-lg ml-2">
                                {{ ucfirst($alert->status) }}
                            </span>
                            @if($alert->isExpired())
                                <span class="badge badge-secondary badge-lg ml-2">Expired</span>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-{{ $alert->severity_color }}">
                            <p class="mb-0">{{ $alert->message }}</p>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <p><strong>Facility:</strong> {{ $alert->facility->name ?? 'N/A' }}</p>
                                <p><strong>Sent By:</strong> {{ $alert->creator->name ?? 'N/A' }}</p>
                                <p><strong>Sent At:</strong> {{ $alert->sent_at ? $alert->sent_at->format('d/m/Y H:i') : 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Target Audience:</strong>
                                    @foreach($alert->target_audience ?? [] as $audience)
                                        <span class="badge badge-light">{{ ucfirst($audience) }}</span>
                                    @endforeach
                                </p>
                                <p><strong>Total Recipients:</strong> {{ $alert->total_recipients }}</p>
                                <p><strong>Acknowledged:</strong> {{ $alert->total_acknowledged }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h4>Quick Stats</h4>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <div class="chart-container" style="position: relative; height: 200px;">
                                <canvas id="acknowledgmentChart"></canvas>
                            </div>
                        </div>
                        <div class="row text-center">
                            <div class="col-6">
                                <h5>{{ $alert->total_acknowledged }}</h5>
                                <small class="text-muted">Acknowledged</small>
                            </div>
                            <div class="col-6">
                                <h5>{{ $alert->total_recipients - $alert->total_acknowledged }}</h5>
                                <small class="text-muted">Pending</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recipients List --}}
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Recipients</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Recipient</th>
                                        <th>Channel</th>
                                        <th>Status</th>
                                        <th>Acknowledged</th>
                                        <th>Sent At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recipients as $recipient)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div>
                                                        <div>{{ $recipient->recipient_type == 'App\Models\User' ? $recipient->recipient->name ?? 'N/A' : 'N/A' }}</div>
                                                        <small class="text-muted">{{ $recipient->phone ?? 'No phone' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ ucfirst($recipient->channel) }}</td>
                                            <td>
                                                <span class="badge badge-{{ $recipient->status == 'sent' ? 'success' : ($recipient->status == 'failed' ? 'danger' : 'warning') }}">
                                                    {{ ucfirst($recipient->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($recipient->isAcknowledged())
                                                    <span class="badge badge-success">
                                                        <i class="fas fa-check-circle me-1"></i> Yes
                                                    </span>
                                                @else
                                                    <span class="badge badge-secondary">No</span>
                                                @endif
                                            </td>
                                            <td>{{ $recipient->sent_at ? $recipient->sent_at->format('d/m/Y H:i') : '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">No recipients found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{ $recipients->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('acknowledgmentChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Acknowledged', 'Pending'],
                datasets: [{
                    data: [
                        {{ $alert->total_acknowledged }},
                        {{ $alert->total_recipients - $alert->total_acknowledged }}
                    ],
                    backgroundColor: ['#4f46e5', '#e2e8f0'],
                    borderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });
    });
</script>
@endpush
@endsection