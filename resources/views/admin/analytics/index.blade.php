@extends('admin.layouts.master')

@section('title', 'Analytics Dashboard')

@section('main-content')
<section class="section">
    <div class="section-header">
        <h1>📊 Analytics Dashboard</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></div>
            <div class="breadcrumb-item active">Analytics</div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="section-body">
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="form-group">
                    <label>Facility</label>
                    <select id="facility-filter" class="form-control" onchange="refreshData()" {{ !$isSuperAdmin ? 'disabled' : '' }}>
                        @if($isSuperAdmin)
                            <option value="">All Facilities</option>
                        @endif
                        @foreach($facilities ?? [] as $facility)
                            <option value="{{ $facility->id }}" {{ $userFacility == $facility->id ? 'selected' : '' }}>
                                {{ $facility->name }} ({{ ucfirst($facility->type) }})
                            </option>
                        @endforeach
                    </select>
                    @if(!$isSuperAdmin)
                        <small class="text-muted">You are viewing data for your facility only.</small>
                    @endif
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Period</label>
                    <select id="period-filter" class="form-control" onchange="refreshData()">
                        <option value="daily">Daily</option>
                        <option value="weekly">Weekly</option>
                        <option value="monthly" selected>Monthly</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>&nbsp;</label>
                    <button class="btn btn-primary w-100" onclick="refreshData()">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                </div>
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="row" id="stats-cards">
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-primary">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Total Visitors</h4>
                        </div>
                        <div class="card-body" id="total-visitors">0</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-success">
                        <i class="fas fa-sign-in-alt"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Checked In</h4>
                        </div>
                        <div class="card-body" id="checked-in">0</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-danger">
                        <i class="fas fa-sign-out-alt"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Checked Out</h4>
                        </div>
                        <div class="card-body" id="checked-out">0</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-warning">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Today</h4>
                        </div>
                        <div class="card-body" id="today-count">0</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Host Stats Cards --}}
        <div class="row" id="host-stats-cards">
            <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-info">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4 id="host-label">Total Hosts</h4>
                        </div>
                        <div class="card-body" id="total-hosts">0</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-purple">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>This Month</h4>
                        </div>
                        <div class="card-body" id="this-month">0</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-pink">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Pending Approvals</h4>
                        </div>
                        <div class="card-body" id="pending-approvals">0</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Charts Row 1 --}}
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h4>Visitor Trends</h4>
                    </div>
                    <div class="card-body">
                        <canvas id="trendsChart" height="250"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h4>Status Distribution</h4>
                    </div>
                    <div class="card-body">
                        <canvas id="statusChart" height="250"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Charts Row 2 --}}
        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4>Peak Hours</h4>
                    </div>
                    <div class="card-body">
                        <canvas id="peakHoursChart" height="200"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4 id="top-hosts-title">Top Employees</h4>
                    </div>
                    <div class="card-body">
                        <canvas id="hostsChart" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Breakdown Chart (Facility-type specific) --}}
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 id="breakdown-title">Department Breakdown</h4>
                    </div>
                    <div class="card-body">
                        <canvas id="breakdownChart" height="150"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent Activity Table --}}
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Recent Visitor Activity</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive" id="activity-table">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Visitor</th>
                                        <th>Host</th>
                                        <th>Time In</th>
                                        <th>Time Out</th>
                                        <th>Purpose</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="activity-body">
                                    <tr><td colspan="6" class="text-center">Loading...</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let trendsChart, statusChart, peakHoursChart, hostsChart, breakdownChart;

    function refreshData() {
        const facilityId = document.getElementById('facility-filter').value;
        const period = document.getElementById('period-filter').value;

        fetchStats(facilityId);
        fetchTrends(facilityId, period);
        fetchPeakHours(facilityId);
        fetchTopHosts(facilityId);
        fetchStatus(facilityId);
        fetchActivity(facilityId);
        fetchBreakdown(facilityId);
    }

    function fetchStats(facilityId) {
        const url = `/api/analytics/stats${facilityId ? '?facility_id=' + facilityId : ''}`;
        fetch(url)
            .then(res => res.json())
            .then(data => {
                document.getElementById('total-visitors').textContent = data.total_visitors || 0;
                document.getElementById('checked-in').textContent = data.checked_in || 0;
                document.getElementById('checked-out').textContent = data.checked_out || 0;
                document.getElementById('today-count').textContent = data.today || 0;
                document.getElementById('total-hosts').textContent = data.total_hosts || 0;
                document.getElementById('this-month').textContent = data.this_month || 0;
                document.getElementById('host-label').textContent = 'Total ' + (data.host_label || 'Hosts');
                document.getElementById('top-hosts-title').textContent = 'Top ' + (data.host_label || 'Employees');
                document.getElementById('pending-approvals').textContent = data.pending_approvals || 0;
            })
            .catch(err => console.error('Stats error:', err));
    }

    function fetchTrends(facilityId, period) {
        const url = `/api/analytics/trends?period=${period}${facilityId ? '&facility_id=' + facilityId : ''}`;
        fetch(url)
            .then(res => res.json())
            .then(data => {
                const labels = data.data.map(d => d.period);
                const values = data.data.map(d => d.count);

                if (trendsChart) {
                    trendsChart.destroy();
                }

                const ctx = document.getElementById('trendsChart').getContext('2d');
                trendsChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Visitors',
                            data: values,
                            backgroundColor: 'rgba(79, 70, 229, 0.2)',
                            borderColor: 'rgba(79, 70, 229, 1)',
                            borderWidth: 2,
                            tension: 0.4,
                            fill: true,
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { stepSize: 1 }
                            }
                        }
                    }
                });
            })
            .catch(err => console.error('Trends error:', err));
    }

    function fetchPeakHours(facilityId) {
        const url = `/api/analytics/peak-hours${facilityId ? '?facility_id=' + facilityId : ''}`;
        fetch(url)
            .then(res => res.json())
            .then(data => {
                const labels = data.map(d => d.hour + ':00');
                const values = data.map(d => d.count);

                if (peakHoursChart) {
                    peakHoursChart.destroy();
                }

                const ctx = document.getElementById('peakHoursChart').getContext('2d');
                peakHoursChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Visitors',
                            data: values,
                            backgroundColor: 'rgba(16, 185, 129, 0.6)',
                            borderColor: 'rgba(16, 185, 129, 1)',
                            borderWidth: 1,
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { stepSize: 1 }
                            }
                        }
                    }
                });
            })
            .catch(err => console.error('Peak hours error:', err));
    }

    function fetchTopHosts(facilityId) {
        const url = `/api/analytics/top-hosts${facilityId ? '?facility_id=' + facilityId : ''}`;
        fetch(url)
            .then(res => res.json())
            .then(data => {
                const labels = data.map(d => d.name || 'Unknown');
                const values = data.map(d => d.total);

                if (hostsChart) {
                    hostsChart.destroy();
                }

                const ctx = document.getElementById('hostsChart').getContext('2d');
                hostsChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Visitors',
                            data: values,
                            backgroundColor: 'rgba(245, 158, 11, 0.6)',
                            borderColor: 'rgba(245, 158, 11, 1)',
                            borderWidth: 1,
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            x: {
                                beginAtZero: true,
                                ticks: { stepSize: 1 }
                            }
                        }
                    }
                });
            })
            .catch(err => console.error('Hosts error:', err));
    }

    function fetchStatus(facilityId) {
        const url = `/api/analytics/status-distribution${facilityId ? '?facility_id=' + facilityId : ''}`;
        fetch(url)
            .then(res => res.json())
            .then(data => {
                if (statusChart) {
                    statusChart.destroy();
                }

                const ctx = document.getElementById('statusChart').getContext('2d');
                statusChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Checked In', 'Checked Out', 'Pending'],
                        datasets: [{
                            data: [data.checked_in || 0, data.checked_out || 0, data.pending || 0],
                            backgroundColor: ['#4f46e5', '#ef4444', '#f59e0b'],
                            borderWidth: 2,
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            })
            .catch(err => console.error('Status error:', err));
    }

    function fetchBreakdown(facilityId) {
        const url = `/api/analytics/breakdown${facilityId ? '?facility_id=' + facilityId : ''}`;
        fetch(url)
            .then(res => res.json())
            .then(data => {
                const labels = data.map(d => d.name || 'N/A');
                const values = data.map(d => d.count);

                if (breakdownChart) {
                    breakdownChart.destroy();
                }

                const ctx = document.getElementById('breakdownChart').getContext('2d');
                breakdownChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Count',
                            data: values,
                            backgroundColor: 'rgba(236, 72, 153, 0.6)',
                            borderColor: 'rgba(236, 72, 153, 1)',
                            borderWidth: 1,
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { stepSize: 1 }
                            }
                        }
                    }
                });
            })
            .catch(err => console.error('Breakdown error:', err));
    }

    function fetchActivity(facilityId) {
        const url = `/api/analytics/daily-activity${facilityId ? '?facility_id=' + facilityId : ''}`;
        fetch(url)
            .then(res => res.json())
            .then(data => {
                const tbody = document.getElementById('activity-body');
                if (!data || data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="6" class="text-center">No activity found</td></tr>';
                    return;
                }
                tbody.innerHTML = data.map(item => `
                    <tr>
                        <td>${item.visitor_name || 'N/A'}</td>
                        <td>${item.host_name || 'N/A'}</td>
                        <td>${item.checkin_time || '-'}</td>
                        <td>${item.checkout_time || '-'}</td>
                        <td>${item.purpose || '-'}</td>
                        <td><span class="badge badge-${item.status === 'approved' ? 'success' : (item.status === 'pending' ? 'warning' : 'danger')}">${item.status || 'pending'}</span></td>
                    </tr>
                `).join('');
            })
            .catch(err => {
                document.getElementById('activity-body').innerHTML = '<tr><td colspan="6" class="text-center text-danger">Error loading activity</td></tr>';
                console.error('Activity error:', err);
            });
    }

    // Load data on page load
    document.addEventListener('DOMContentLoaded', function() {
        refreshData();

        // Auto-refresh every 60 seconds
        setInterval(refreshData, 60000);
    });
</script>
@endpush
@endsection