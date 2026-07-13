@extends('pwa.index')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0">👋 Hello, {{ Auth::user()->first_name }}</h5>
    <span class="badge bg-info">Resident</span>
</div>

<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col-6">
        <div class="card-stat text-center">
            <div class="number" id="my-guests-count">0</div>
            <div class="label">My Guests</div>
        </div>
    </div>
    <div class="col-6">
        <div class="card-stat text-center">
            <div class="number" id="pending-guest-approvals">0</div>
            <div class="label">Pending Approvals</div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row g-3 mb-4">
    <div class="col-4">
        <a href="{{ route('pwa.page', 'visitors') }}" class="quick-action d-block">
            <i class="fas fa-users"></i>
            <span>Guests</span>
        </a>
    </div>
    <div class="col-4">
        <a href="{{ route('pwa.page', 'scan') }}" class="quick-action d-block">
            <i class="fas fa-qrcode"></i>
            <span>QR</span>
        </a>
    </div>
    <div class="col-4">
        <button class="quick-action d-block" onclick="generateOTP()">
            <i class="fas fa-key"></i>
            <span>OTP</span>
        </button>
    </div>
</div>

<!-- Pending Guest Approvals -->
<div class="d-flex justify-content-between align-items-center mb-2">
    <h6 class="fw-bold mb-0">Pending Approvals</h6>
</div>
<div id="pending-list">
    <div class="text-center text-muted py-4">
        <i class="fas fa-spinner fa-spin fa-2x"></i>
        <p class="mt-2">Loading...</p>
    </div>
</div>

@push('scripts')
<script>
    function loadStats() {
        fetch('/api/pwa/resident-stats')
            .then(res => res.json())
            .then(data => {
                document.getElementById('my-guests-count').textContent = data.total || 0;
                document.getElementById('pending-guest-approvals').textContent = data.pending || 0;
            })
            .catch(err => console.log('Stats error:', err));
    }

    function loadPending() {
        fetch('/api/pwa/pending-guest-approvals')
            .then(res => res.json())
            .then(data => {
                const container = document.getElementById('pending-list');
                if (data.length === 0) {
                    container.innerHTML = `<div class="text-center text-muted py-3">No pending approvals 🎉</div>`;
                    return;
                }
                container.innerHTML = data.map(v => `
                    <div class="list-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-bold">${v.visitor_name || 'Unknown'}</div>
                                <div class="small text-muted">${v.check_in_time || ''}</div>
                            </div>
                            <div>
                                <button class="btn btn-sm btn-success me-1" onclick="approveGuest(${v.id})">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="rejectGuest(${v.id})">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `).join('');
            })
            .catch(err => {
                document.getElementById('pending-list').innerHTML = `
                    <div class="text-center text-muted py-3">Could not load pending approvals</div>
                `;
            });
    }

    function approveGuest(id) {
        if (!confirm('Approve this guest?')) return;
        fetch(`/api/pwa/approve/${id}`, { method: 'POST' })
            .then(res => res.json())
            .then(() => { loadPending(); loadStats(); })
            .catch(err => alert('Error approving guest'));
    }

    function rejectGuest(id) {
        if (!confirm('Reject this guest?')) return;
        fetch(`/api/pwa/reject/${id}`, { method: 'POST' })
            .then(res => res.json())
            .then(() => { loadPending(); loadStats(); })
            .catch(err => alert('Error rejecting guest'));
    }

    function generateOTP() {
        fetch('/api/pwa/generate-otp', { method: 'POST' })
            .then(res => res.json())
            .then(data => {
                alert(`Your OTP is: ${data.otp}\nExpires in 5 minutes.\nShare this with your guest.`);
                // Also send via WhatsApp
                if (data.whatsapp_sent) {
                    alert('OTP also sent via WhatsApp!');
                }
            })
            .catch(err => alert('Error generating OTP'));
    }

    loadStats();
    loadPending();

    // Refresh every 30 seconds
    setInterval(() => { loadStats(); loadPending(); }, 30000);
</script>
@endpush
@endsection