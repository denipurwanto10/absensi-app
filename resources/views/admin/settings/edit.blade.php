@extends('layouts.app')
@section('title', 'Pengaturan')
@section('sidebar')
    @include('layouts._admin-sidebar')
@endsection

@section('content')
    <div class="mb-4">
        <h4 class="mb-1">Pengaturan Sistem</h4>
        <p class="text-muted mb-0">Identitas perusahaan, jam kerja, dan validasi lokasi absen.</p>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-12 col-lg-7">
            <form action="{{ route('admin.settings.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="card p-4 mb-3">
                    <h6 class="mb-3"><i class="bi bi-building"></i> Identitas & Jam Kerja</h6>
                    <div class="mb-3">
                        <label class="form-label">Nama Perusahaan</label>
                        <input type="text" name="company_name" class="form-control" value="{{ old('company_name', $setting->company_name) }}" required>
                    </div>
                    <div class="mb-1">
                        <label class="form-label">Batas Jam Masuk (dianggap telat setelah jam ini)</label>
                        <input type="time" name="batas_telat" class="form-control"
                               value="{{ old('batas_telat', \Illuminate\Support\Carbon::parse($setting->batas_telat)->format('H:i')) }}" required>
                    </div>
                </div>

                <div class="card p-4 mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0"><i class="bi bi-geo-alt"></i> Validasi Lokasi Absen (Geofencing)</h6>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="geofence_enabled"
                                   name="geofence_enabled" value="1" {{ old('geofence_enabled', $setting->geofence_enabled) ? 'checked' : '' }}>
                        </div>
                    </div>
                    <p class="text-muted small">Kalau aktif, karyawan hanya bisa absen mandiri (scan dari HP sendiri) jika berada dalam radius kantor. Scan lewat perangkat admin/kiosk tidak terpengaruh.</p>

                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Latitude Kantor</label>
                            <input type="text" name="office_lat" class="form-control" placeholder="-6.2345678"
                                   value="{{ old('office_lat', $setting->office_lat) }}">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Longitude Kantor</label>
                            <input type="text" name="office_lng" class="form-control" placeholder="106.9876543"
                                   value="{{ old('office_lng', $setting->office_lng) }}">
                        </div>
                    </div>
                    <button type="button" id="btn-use-current" class="btn btn-sm btn-outline-secondary mb-3">
                        <i class="bi bi-crosshair"></i> Gunakan lokasi perangkat ini sebagai titik kantor
                    </button>
                    <div class="mb-1">
                        <label class="form-label">Radius Toleransi (meter)</label>
                        <input type="number" name="radius_meters" class="form-control" min="20" max="5000"
                               value="{{ old('radius_meters', $setting->radius_meters) }}" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-save"></i> Simpan Pengaturan
                </button>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.getElementById('btn-use-current').addEventListener('click', function () {
        if (!navigator.geolocation) {
            alert('Browser tidak mendukung geolocation.');
            return;
        }
        navigator.geolocation.getCurrentPosition(function (pos) {
            document.querySelector('[name=office_lat]').value = pos.coords.latitude.toFixed(7);
            document.querySelector('[name=office_lng]').value = pos.coords.longitude.toFixed(7);
        }, function () {
            alert('Tidak bisa mengambil lokasi. Pastikan izin lokasi diaktifkan.');
        });
    });
</script>
@endpush
