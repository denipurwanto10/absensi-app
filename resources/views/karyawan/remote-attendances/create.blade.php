@extends('layouts.app')
@section('title', 'Absen Luar Kantor')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Absen Luar Kantor</h4>
            <p class="text-muted mb-0">Untuk dinas, kerja lapangan, atau WFH. Wajib foto + lokasi, menunggu persetujuan admin.</p>
        </div>
        <a href="{{ route('karyawan.remote-attendances.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6">
            <div class="alert d-flex align-items-center gap-2 mb-3" style="background:var(--amber-soft); color:var(--amber-ink); border:none;">
                <i class="bi bi-info-circle fs-5"></i>
                <div>Absensi ini <strong>tidak langsung tercatat</strong> — admin akan meninjau foto & lokasimu dulu sebelum masuk ke laporan kehadiran.</div>
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

                <form action="{{ route('karyawan.remote-attendances.store') }}" method="POST" enctype="multipart/form-data" id="remoteForm">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Jenis Absen</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="punch_type" id="punch-masuk" value="masuk" {{ old('punch_type', $suggestedPunchType) === 'masuk' ? 'checked' : '' }}>
                            <label class="btn btn-outline-primary" for="punch-masuk"><i class="bi bi-box-arrow-in-right"></i> Masuk</label>

                            <input type="radio" class="btn-check" name="punch_type" id="punch-pulang" value="pulang" {{ old('punch_type', $suggestedPunchType) === 'pulang' ? 'checked' : '' }}>
                            <label class="btn btn-outline-primary" for="punch-pulang"><i class="bi bi-box-arrow-left"></i> Pulang</label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Kategori</label>
                        <select name="category" class="form-select" required>
                            <option value="">-- Pilih kategori --</option>
                            <option value="dinas" {{ old('category') === 'dinas' ? 'selected' : '' }}>Dinas Luar</option>
                            <option value="lapangan" {{ old('category') === 'lapangan' ? 'selected' : '' }}>Kerja Lapangan (Sales/Kunjungan)</option>
                            <option value="wfh" {{ old('category') === 'wfh' ? 'selected' : '' }}>Work From Home (WFH)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Foto Bukti</label>
                        <input type="file" name="photo" id="photo-input" class="form-control" accept="image/*" capture="environment" required>
                        <small class="text-muted">Ambil foto langsung dari kamera (selfie/kondisi lokasi). Maks 3MB.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Keterangan (opsional)</label>
                        <textarea name="note" class="form-control" rows="2" placeholder="Contoh: Kunjungan klien PT Maju Jaya.">{{ old('note') }}</textarea>
                    </div>

                    <input type="hidden" name="lat" id="lat-input">
                    <input type="hidden" name="lng" id="lng-input">

                    <div class="alert py-2 px-3 small mb-3 d-flex align-items-center gap-2" id="loc-status" style="background: var(--mist); border: 1px solid var(--line); color: var(--muted);">
                        <i class="bi bi-geo-alt"></i> <span>Mendeteksi lokasi kamu...</span>
                    </div>

                    <button type="submit" class="btn btn-primary w-100" id="submitBtn" disabled>
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
        const latInput = document.getElementById('lat-input');
        const lngInput = document.getElementById('lng-input');
        const submitBtn = document.getElementById('submitBtn');

        function setStatus(html, ok) {
            locStatus.innerHTML = html;
            locStatus.style.background = ok ? 'var(--teal-soft)' : 'var(--coral-soft)';
            locStatus.style.color = ok ? 'var(--teal-dark)' : 'var(--coral)';
            locStatus.style.borderColor = 'transparent';
        }

        if (!navigator.geolocation) {
            setStatus('<i class="bi bi-exclamation-triangle"></i> Perangkat/browser kamu tidak mendukung lokasi GPS.', false);
            return;
        }

        navigator.geolocation.getCurrentPosition(function (pos) {
            latInput.value = pos.coords.latitude;
            lngInput.value = pos.coords.longitude;
            setStatus('<i class="bi bi-geo-alt-fill"></i> Lokasi terdeteksi (akurasi ~' + Math.round(pos.coords.accuracy) + 'm).', true);
            submitBtn.disabled = false;
        }, function () {
            setStatus('<i class="bi bi-exclamation-triangle"></i> Izin lokasi ditolak. Aktifkan lokasi di browser lalu muat ulang halaman ini.', false);
        }, { enableHighAccuracy: true, timeout: 10000 });
    })();
</script>
@endpush
