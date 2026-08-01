@extends('layouts.app')
@section('title', 'Absen QR')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h4 class="mb-1">Absen dengan QR Code</h4>
                    <p class="text-muted mb-0 small">Scan pertama = masuk, scan kedua = pulang.</p>
                </div>
                <a href="{{ route('karyawan.dashboard') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>

            <div class="card p-3">
                <div id="reader" class="overflow-hidden rounded-3" style="width:100%;"></div>
                <div id="result-box" class="alert d-none mt-3 mb-0" role="alert"></div>
            </div>
            <p class="text-muted small mt-2 text-center">
                <i class="bi bi-info-circle"></i>
                Arahkan kamera ke QR Code pribadimu (bisa dilihat di halaman Dashboard).
            </p>
            @if($setting->geofence_enabled)
                <p id="loc-status" class="text-muted small text-center">
                    <i class="bi bi-geo-alt"></i> Mendeteksi lokasi kamu...
                </p>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    const resultBox = document.getElementById('result-box');
    const geofenceEnabled = @json($setting->geofence_enabled);
    let isProcessing = false;
    let currentPosition = null;

    function showResult(message, type) {
        resultBox.className = 'alert alert-' + type + ' mt-3 mb-0';
        resultBox.textContent = message;
        resultBox.classList.remove('d-none');
    }

    if (geofenceEnabled && navigator.geolocation) {
        const locStatus = document.getElementById('loc-status');
        navigator.geolocation.getCurrentPosition(function (pos) {
            currentPosition = pos.coords;
            if (locStatus) locStatus.innerHTML = '<i class="bi bi-geo-alt-fill text-success"></i> Lokasi terdeteksi.';
        }, function () {
            if (locStatus) locStatus.innerHTML = '<i class="bi bi-exclamation-triangle text-danger"></i> Izin lokasi ditolak. Absen mungkin gagal.';
        });
    }

    async function onScanSuccess(decodedText) {
        if (isProcessing) return;
        isProcessing = true;

        try {
            const res = await fetch("{{ route('karyawan.attendances.scan') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    qr_token: decodedText,
                    lat: currentPosition ? currentPosition.latitude : null,
                    lng: currentPosition ? currentPosition.longitude : null,
                }),
            });
            const data = await res.json();
            showResult(data.message, data.success ? 'success' : 'warning');
        } catch (e) {
            showResult('Terjadi kesalahan koneksi.', 'danger');
        }

        setTimeout(() => { isProcessing = false; }, 3000);
    }

    const html5QrCode = new Html5Qrcode("reader");
    html5QrCode.start(
        { facingMode: "environment" },
        { fps: 10, qrbox: { width: 250, height: 250 } },
        onScanSuccess
    ).catch((err) => {
        showResult('Tidak bisa mengakses kamera: ' + err, 'danger');
    });
</script>
@endpush
