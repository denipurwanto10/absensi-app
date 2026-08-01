@extends('layouts.app')
@section('title', 'QR Code Karyawan')
@section('sidebar')
    @include('layouts._admin-sidebar')
@endsection

@section('content')
    <div class="mb-4">
        <h4 class="mb-1">QR Code Karyawan</h4>
        <p class="text-muted mb-0">Cetak atau tunjukkan langsung dari layar ke perangkat scanner.</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-sm-8 col-md-5 col-lg-4">
            <div class="card p-4 text-center" id="qr-card">
                <img src="{{ $employee->photo_url }}" alt="Foto" class="rounded-circle mx-auto mb-2" width="64" height="64" style="object-fit:cover;">
                <h5 class="mb-0">{{ $employee->user->name }}</h5>
                <small class="text-muted">NIP: {{ $employee->nip }}</small>
                <div class="d-flex justify-content-center my-3">
                    <div id="qrcode"></div>
                </div>
                <small class="text-muted">Tunjukkan QR ini ke perangkat scanner untuk absen masuk/pulang.</small>
            </div>

            <div class="d-flex gap-2 mt-3">
                <button class="btn btn-primary w-50" onclick="window.print()"><i class="bi bi-printer"></i> Cetak</button>
                <form action="{{ route('admin.employees.regenerate-qr', $employee) }}" method="POST" class="w-50" data-confirm="QR code lama akan langsung tidak berlaku setelah ini. Karyawan harus memindai QR baru untuk absen." data-confirm-type="warning" data-confirm-title="Buat Ulang QR Code?" data-confirm-ok="Ya, Buat Ulang">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger w-100"><i class="bi bi-arrow-repeat"></i> Buat Ulang</button>
                </form>
            </div>
            <a href="{{ route('admin.employees.index') }}" class="btn btn-link w-100 mt-2">Kembali</a>
        </div>
    </div>

    <style>
        @media print {
            .navbar, .sidebar, .btn, form, a.btn-link { display: none !important; }
            #qr-card { border: 1px solid #ccc; box-shadow: none; }
        }
    </style>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
    new QRCode(document.getElementById("qrcode"), {
        text: "{{ $employee->qr_token }}",
        width: 200,
        height: 200,
    });
</script>
@endpush
