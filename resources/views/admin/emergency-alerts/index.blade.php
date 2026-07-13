@extends('admin.layouts.master')

@section('title', 'Emergency Alerts')

@section('main-content')
<section class="section">
    <div class="section-header">
        <h1>🚨 Emergency Alerts</h1>
        <div class="section-header-button">
            <a href="{{ route('admin.emergency-alerts.create') }}" class="btn btn-danger">
                <i class="fas fa-plus-circle me-2"></i>Send New Alert
            </a>
        </div>
    </div>

    <div class="section-body">
        {{-- Stats --}}
        <div class="row">
            <div class="col-lg-2 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-primary">
                        <i class="fas fa-bell"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Total Alerts</h4>
                        </div>
                        <div class="card-body">
                            {{ $stats['total'] }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-success">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Sent</h4>
                        </div>
                        <div class="card-body">
                            {{ $stats['sent'] }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-warning">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Pending</h4>
                        </div>
                        <div class="card-body">
                            {{ $stats['pending'] }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Emergency</h4>
                        </div>
                        <div class="card-body">
                            {{ $stats['emergency'] }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-info">
                        <i class="fas fa-check-double"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Acknowledged</h4>
                        </div>
                        <div class="card-body">
                            {{ $stats['acknowledged'] }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-secondary">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Expired</h4>
                        </div>
                        <div class="card-body">
                            {{ $stats['expired'] }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Alerts Table --}}
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Alert History</h4>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Severity</th>
                                        <th>Title</th>
                                        <th>Audience</th>
                                        <th>Recipients</th>
                                        <th>Acknowledged</th>
                                        <th>Sent At</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($alerts as $alert)
                                        <tr>
                                            <td>{{ $alert->id }}</td>
                                            <td>
                                                <span class="badge badge-{{ $alert->severity_color }}">
                                                    <i class="{{ $alert->severity_icon }} me-1"></i>
                                                    {{ ucfirst($alert->severity) }}
                                                </span>
                                            </td>
                                            <td>{{ $alert->title }}</td>
                                            <td>
                                                @foreach($alert->target_audience ?? [] as $audience)
                                                    <span class="badge badge-light">{{ ucfirst($audience) }}</span>
                                                @endforeach
                                            </td>
                                            <td>{{ $alert->total_recipients }}</td>
                                            <td>{{ $alert->total_acknowledged }}</td>
                                            <td>{{ $alert->sent_at ? $alert->sent_at->format('d/m/Y H:i') : '-' }}</td>
                                            <td>
                                                <span class="badge badge-{{ $alert->status == 'sent' ? 'success' : ($alert->status == 'failed' ? 'danger' : 'warning') }}">
                                                    {{ ucfirst($alert->status) }}
                                                </span>
                                                @if($alert->isExpired())
                                                    <span class="badge badge-secondary">Expired</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.emergency-alerts.show', $alert->id) }}" class="btn btn-sm btn-info">
                                                    <i class="far fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center">No alerts found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{ $alerts->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection