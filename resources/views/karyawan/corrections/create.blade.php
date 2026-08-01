@extends('layouts.app')
@section('title', 'Ajukan Koreksi Absensi')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Ajukan Koreksi Absensi</h4>
            <p class="text-muted mb-0">Lupa scan, atau jam yang tercatat salah? Ajukan koreksi di sini.</p>
        </div>
        <a href="{{ route('karyawan.corrections.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6">
            <div class="alert d-flex align-items-center gap-2 mb-3" style="background:var(--amber-soft); color:var(--amber-ink); border:none;">
                <i class="bi bi-info-circle fs-5"></i>
                <div>Isi minimal salah satu: jam masuk atau jam pulang yang seharusnya. Field yang dikosongkan tidak akan diubah.</div>
            </div>

            <div class="card p-4">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $e)
                                <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('karyawan.corrections.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Tanggal Absensi</label>
                        <input type="date" name="date" class="form-control" value="{{ old('date') }}" max="{{ now()->toDateString() }}" required>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Jam Masuk Seharusnya</label>
                            <input type="time" name="requested_check_in" class="form-control" value="{{ old('requested_check_in') }}">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Jam Pulang Seharusnya</label>
                            <input type="time" name="requested_check_out" class="form-control" value="{{ old('requested_check_out') }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alasan</label>
                        <textarea name="reason" class="form-control" rows="3" placeholder="Contoh: Lupa scan QR saat masuk, sebenarnya sudah hadir jam 07:45." required>{{ old('reason') }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lampiran Bukti (opsional)</label>
                        <input type="file" name="attachment" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                        <small class="text-muted">Maks 2MB (jpg/png/pdf).</small>
                    </div>

                    <input type="hidden" name="submit_lat" id="submit-lat">
                    <input type="hidden" name="submit_lng" id="submit-lng">
                    <div class="alert py-2 px-3 small mb-3 d-flex align-items-center gap-2" id="loc-status" style="background: var(--mist); border: 1px solid var(--line); color: var(--muted);">
                        <i class="bi bi-geo-alt"></i> <span>Menandai lokasimu saat ini (opsional, membantu admin verifikasi)...</span>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-send"></i> Kirim Pengajuan
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    (function () {
        const locStatus = document.getElementById('loc-status');
        const latInput = document.getElementById('submit-lat');
        const lngInput = document.getElementById('submit-lng');

        function setStatus(html, ok) {
            locStatus.innerHTML = html;
            locStatus.style.background = ok ? 'var(--teal-soft)' : 'var(--amber-soft)';
            locStatus.style.color = ok ? 'var(--teal-dark)' : 'var(--amber-ink)';
            locStatus.style.borderColor = 'transparent';
        }

        if (!navigator.geolocation) return;

        navigator.geolocation.getCurrentPosition(function (pos) {
            latInput.value = pos.coords.latitude;
            lngInput.value = pos.coords.longitude;
            setStatus('<i class="bi bi-geo-alt-fill"></i> Lokasi berhasil ditandai, akan ditampilkan ke admin.', true);
        }, function () {
            setStatus('<i class="bi bi-geo-alt"></i> Lokasi tidak diizinkan/tersedia. Pengajuan tetap bisa dikirim tanpa lokasi.', false);
        }, { enableHighAccuracy: true, timeout: 8000 });
    })();
</script>
@endpush
