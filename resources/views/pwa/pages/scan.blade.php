@extends('pwa.index')

@section('content')
<div class="text-center mb-3">
    <h5 class="fw-bold">Scan QR Code</h5>
    <p class="text-muted small">Point your camera at the visitor's QR code</p>
</div>

<div id="scanner-container" style="position:relative; width:100%; max-width:400px; margin:0 auto;">
    <div id="qr-reader" style="width:100%;"></div>
    <div id="qr-reader-results" class="mt-3"></div>
</div>

<div id="manual-entry" class="mt-3">
    <p class="text-muted small text-center">Or enter manually</p>
    <div class="input-group">
        <input type="text" id="manual-qr-input" class="form-control" placeholder="Enter QR code">
        <button class="btn btn-primary" onclick="manualCheck()">
            <i class="fas fa-search"></i>
        </button>
    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    let html5QrCode;

    function startScanner() {
        const container = document.getElementById('qr-reader');
        html5QrCode = new Html5Qrcode("qr-reader");

        html5QrCode.start(
            { facingMode: "environment" },
            { fps: 10, qrbox: { width: 250, height: 250 } },
            onScanSuccess,
            onScanError
        ).catch(err => {
            container.innerHTML = `
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Camera access denied. Please use manual entry below.
                </div>
            `;
        });
    }

    function onScanSuccess(decodedText, decodedResult) {
        // Stop scanning
        html5QrCode.stop();
        
        // Show result
        document.getElementById('qr-reader-results').innerHTML = `
            <div class="alert alert-success">
                <i class="fas fa-check-circle me-2"></i>
                QR Code scanned: <strong>${decodedText}</strong>
            </div>
            <button class="btn btn-success w-100" onclick="checkinVisitor('${decodedText}')">
                <i class="fas fa-sign-in-alt me-2"></i>Check In Visitor
            </button>
        `;
    }

    function onScanError(err) {
        // Ignore errors (they happen continuously while scanning)
    }

    function checkinVisitor(qrCode) {
        fetch('/api/pwa/checkin', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ qr_code: qrCode })
        })
        .then(res => res.json())
        .then(data => {
            alert('✅ Visitor checked in successfully!');
            document.getElementById('qr-reader-results').innerHTML = `
                <div class="alert alert-success">✅ ${data.message}</div>
            `;
            setTimeout(() => {
                document.getElementById('qr-reader-results').innerHTML = '';
                startScanner();
            }, 3000);
        })
        .catch(err => {
            alert('❌ Error checking in visitor. Please try again.');
            document.getElementById('qr-reader-results').innerHTML = `
                <div class="alert alert-danger">❌ ${err.message}</div>
            `;
        });
    }

    function manualCheck() {
        const input = document.getElementById('manual-qr-input');
        if (input.value) {
            checkinVisitor(input.value);
        } else {
            alert('Please enter a QR code');
        }
    }

    // Start scanner
    startScanner();
</script>
@endpush
@endsection