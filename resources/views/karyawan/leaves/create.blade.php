@extends('layouts.app')
@section('title', 'Ajukan Izin')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Ajukan Izin / Sakit / Cuti</h4>
            <p class="text-muted mb-0">Isi form berikut, admin akan meninjau pengajuanmu.</p>
        </div>
        <a href="{{ route('karyawan.leaves.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6">
            <div class="alert d-flex align-items-center gap-2 mb-3" style="background:var(--teal-soft); color:var(--teal-dark); border:none;">
                <i class="bi bi-airplane fs-5"></i>
                <div>Sisa kuota cuti kamu tahun {{ now()->year }}: <strong>{{ $cutiRemaining }} dari {{ $cutiQuota }} hari</strong>. Kuota hanya berlaku untuk jenis "Cuti" (izin & sakit tidak memotong kuota).</div>
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

                <form action="{{ route('karyawan.leaves.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Jenis Pengajuan</label>
                        <select name="type" id="leave-type" class="form-select" required>
                            <option value="">-- Pilih jenis --</option>
                            <option value="izin" {{ old('type') === 'izin' ? 'selected' : '' }}>Izin</option>
                            <option value="sakit" {{ old('type') === 'sakit' ? 'selected' : '' }}>Sakit</option>
                            <option value="cuti" {{ old('type') === 'cuti' ? 'selected' : '' }}>Cuti</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Tanggal Mulai</label>
                            <input type="date" name="start_date" id="leave-start" class="form-control" value="{{ old('start_date') }}" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Tanggal Selesai</label>
                            <input type="date" name="end_date" id="leave-end" class="form-control" value="{{ old('end_date') }}" required>
                        </div>
                    </div>
                    <div id="leave-quota-warning" class="alert alert-danger py-2 small d-none"></div>
                    <div class="mb-3">
                        <label class="form-label">Alasan</label>
                        <textarea name="reason" class="form-control" rows="3" required>{{ old('reason') }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lampiran Bukti (opsional)</label>
                        <input type="file" name="attachment" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                        <small class="text-muted">Contoh: surat dokter untuk sakit. Maks 2MB (jpg/png/pdf).</small>
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
        const cutiRemaining = {{ (int) $cutiRemaining }};
        const typeEl = document.getElementById('leave-type');
        const startEl = document.getElementById('leave-start');
        const endEl = document.getElementById('leave-end');
        const warnEl = document.getElementById('leave-quota-warning');

        function checkQuota() {
            if (typeEl.value !== 'cuti' || !startEl.value || !endEl.value) {
                warnEl.classList.add('d-none');
                return;
            }
            const start = new Date(startEl.value);
            const end = new Date(endEl.value);
            const days = Math.round((end - start) / 86400000) + 1;

            if (days > 0 && days > cutiRemaining) {
                warnEl.textContent = `Pengajuan ${days} hari melebihi sisa kuota cuti kamu (${cutiRemaining} hari). Pengajuan tetap bisa dikirim tapi kemungkinan besar ditolak admin.`;
                warnEl.classList.remove('d-none');
            } else {
                warnEl.classList.add('d-none');
            }
        }

        [typeEl, startEl, endEl].forEach(el => el.addEventListener('change', checkQuota));
    })();
</script>
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
