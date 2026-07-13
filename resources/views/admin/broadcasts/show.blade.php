@extends('admin.layouts.master')

@section('title', 'Broadcast Details')

@section('main-content')
<section class="section">
    <div class="section-header">
        <h1>📢 Broadcast Details</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="{{ route('admin.broadcasts.index') }}">Broadcasts</a></div>
            <div class="breadcrumb-item active">Details</div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4>{{ $broadcast->title }}</h4>
                        <div class="card-header-action">
                            <span class="badge badge-{{ $broadcast->status_color }} badge-lg">
                                {{ ucfirst($broadcast->status) }}
                            </span>
                            <span class="badge badge-info badge-lg ml-2">
                                <i class="{{ $broadcast->channel_icon }} me-1"></i>
                                {{ ucfirst($broadcast->channel) }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-light">
                            <p class="mb-0">{{ $broadcast->message }}</p>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <p><strong>Facility:</strong> {{ $broadcast->facility->name ?? 'N/A' }}</p>
                                <p><strong>Sent By:</strong> {{ $broadcast->creator->name ?? 'N/A' }}</p>
                                <p><strong>Channel:</strong> {{ ucfirst($broadcast->channel) }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Target Audience:</strong></p>
                                <p>{{ $broadcast->target_groups_label }}</p>
                                <p><strong>Sent At:</strong> {{ $broadcast->sent_at ? $broadcast->sent_at->format('d/m/Y H:i') : 'Not sent yet' }}</p>
                                @if($broadcast->scheduled_at)
                                    <p><strong>Scheduled At:</strong> {{ $broadcast->scheduled_at->format('d/m/Y H:i') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h4>Statistics</h4>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <div class="chart-container" style="position: relative; height: 200px;">
                                <canvas id="deliveryChart"></canvas>
                            </div>
                        </div>
                        <div class="row text-center">
                            <div class="col-4">
                                <h5>{{ $broadcast->total_recipients }}</h5>
                                <small class="text-muted">Total</small>
                            </div>
                            <div class="col-4">
                                <h5 class="text-success">{{ $broadcast->total_delivered }}</h5>
                                <small class="text-muted">Delivered</small>
                            </div>
                            <div class="col-4">
                                <h5 class="text-danger">{{ $broadcast->total_failed }}</h5>
                                <small class="text-muted">Failed</small>
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
                                                <span class="badge badge-{{ $recipient->status == 'sent' ? 'success' : ($recipient->status == 'delivered' ? 'info' : ($recipient->status == 'failed' ? 'danger' : 'warning')) }}">
                                                    {{ ucfirst($recipient->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $recipient->sent_at ? $recipient->sent_at->format('d/m/Y H:i') : '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">No recipients found.</td>
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

        <div class="mt-3">
            <a href="{{ route('admin.broadcasts.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back
            </a>
        </div>
    </div>
</section>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('deliveryChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Delivered', 'Failed', 'Pending'],
                datasets: [{
                    data: [
                        {{ $broadcast->total_delivered }},
                        {{ $broadcast->total_failed }},
                        {{ $broadcast->total_recipients - $broadcast->total_delivered - $broadcast->total_failed }}
                    ],
                    backgroundColor: ['#10b981', '#ef4444', '#f59e0b'],
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