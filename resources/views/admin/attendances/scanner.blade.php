@extends('layouts.app')
@section('title', 'Scan Absensi')
@section('sidebar')
    @include('layouts._admin-sidebar')
@endsection

@section('content')
    <div class="mb-4">
        <h4 class="mb-1">Scan Absensi</h4>
        <p class="text-muted mb-0">Arahkan kamera ke QR Code milik karyawan.</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6">
            <div class="card p-3">
                <div id="reader" class="overflow-hidden rounded-3" style="width:100%;"></div>
                <div id="result-box" class="alert d-none mt-3 mb-0" role="alert"></div>
            </div>
            <p class="text-muted small mt-2 text-center">
                <i class="bi bi-info-circle"></i>
                Sistem otomatis mendeteksi apakah ini absen masuk atau pulang.
            </p>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    const resultBox = document.getElementById('result-box');
    let isProcessing = false;

    function showResult(message, type) {
        resultBox.className = 'alert alert-' + type + ' mt-3 mb-0';
        resultBox.textContent = message;
        resultBox.classList.remove('d-none');
    }

    async function onScanSuccess(decodedText) {
        if (isProcessing) return;
        isProcessing = true;

        try {
            const res = await fetch("{{ route('admin.attendances.scan') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ qr_token: decodedText }),
            });
            const data = await res.json();
            showResult(data.message, data.success ? 'success' : 'warning');
        } catch (e) {
            showResult('Terjadi kesalahan koneksi.', 'danger');
        }

        setTimeout(() => { isProcessing = false; }, 3000); // jeda 3 detik sebelum bisa scan lagi
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
