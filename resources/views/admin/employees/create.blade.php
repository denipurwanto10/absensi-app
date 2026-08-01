@extends('layouts.app')
@section('title', 'Tambah Karyawan')
@section('sidebar')
    @include('layouts._admin-sidebar')
@endsection

@push('styles')
<style>
    .ekf-header { display: flex; align-items: center; gap: .9rem; margin-bottom: 1.6rem; }
    .ekf-header-icon {
        width: 48px; height: 48px; border-radius: .9rem; flex: 0 0 auto;
        background: var(--ink-fixed); color: var(--amber);
        display: flex; align-items: center; justify-content: center; font-size: 1.3rem;
        box-shadow: inset 0 0 0 1px rgba(219,154,61,.35);
    }
    .ekf-section + .ekf-section { margin-top: 1.9rem; padding-top: 1.9rem; border-top: 1px dashed var(--line); }
    .ekf-section-title {
        display: flex; align-items: center; gap: .55rem; font-family: var(--display);
        font-weight: 700; font-size: .95rem; color: var(--ink); margin-bottom: 1rem;
    }
    .ekf-section-title .num {
        width: 24px; height: 24px; border-radius: 50%; background: var(--teal-soft); color: var(--teal-dark);
        font-family: var(--mono); font-size: .72rem; font-weight: 700;
        display: flex; align-items: center; justify-content: center; flex: 0 0 auto;
    }
    .ekf-input-group { position: relative; }
    .ekf-input-group i.bi {
        position: absolute; left: .85rem; top: 50%; transform: translateY(-50%);
        color: var(--muted); font-size: .95rem; pointer-events: none;
    }
    .ekf-input-group .form-control { padding-left: 2.35rem; }
    .ekf-actions {
        display: flex; gap: .6rem; justify-content: flex-end;
        margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--line);
    }
    .ekf-preview-card { position: sticky; top: 1.5rem; }
    .ekf-avatar-wrap { width: 112px; height: 112px; margin: 0 auto 1rem; position: relative; }
    .ekf-avatar {
        width: 112px; height: 112px; border-radius: 50%; background: var(--mist);
        border: 3px solid var(--surface); box-shadow: 0 0 0 1px var(--line), 0 8px 20px -8px var(--shadow-3);
        display: flex; align-items: center; justify-content: center; color: var(--muted); font-size: 2.2rem;
    }
    .ekf-avatar-upload {
        position: absolute; bottom: 0; right: 0; width: 34px; height: 34px; border-radius: 50%;
        background: var(--amber); color: var(--ink-fixed); display: flex; align-items: center; justify-content: center;
        border: 3px solid var(--surface); cursor: pointer; font-size: .9rem;
        box-shadow: 0 3px 8px -2px rgba(0,0,0,.3); transition: transform .15s ease;
    }
    .ekf-avatar-upload:hover { transform: scale(1.08); }
    .ekf-tip {
        display: flex; gap: .6rem; padding: .7rem .8rem; border-radius: .7rem; background: var(--mist);
        font-size: .78rem; color: var(--muted); line-height: 1.4; margin-bottom: .55rem;
    }
    .ekf-tip i { color: var(--amber); font-size: .95rem; flex: 0 0 auto; margin-top: .1rem; }
    .ekf-tip:last-child { margin-bottom: 0; }
</style>
@endpush

@section('content')
    <div class="ekf-header">
        <div class="ekf-header-icon"><i class="bi bi-person-plus-fill"></i></div>
        <div>
            <h4 class="mb-1">Tambah Karyawan</h4>
            <p class="text-muted mb-0">Buat akun & data karyawan baru. QR Code presensi dibuat otomatis.</p>
        </div>
    </div>

    <form action="{{ route('admin.employees.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card p-4">

                    <div class="ekf-section">
                        <div class="ekf-section-title"><span class="num">1</span> Akun &amp; Kredensial</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <div class="ekf-input-group">
                                    <i class="bi bi-envelope"></i>
                                    <input type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" required placeholder="nama@perusahaan.com">
                                </div>
                                @error('email') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Password</label>
                                <div class="ekf-input-group">
                                    <i class="bi bi-lock"></i>
                                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required placeholder="Minimal 8 karakter">
                                </div>
                                @error('password') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="ekf-section">
                        <div class="ekf-section-title"><span class="num">2</span> Data Pribadi</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Lengkap</label>
                                <div class="ekf-input-group">
                                    <i class="bi bi-person"></i>
                                    <input type="text" name="name" id="nameInput" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" required placeholder="cth. Siti Amelia">
                                </div>
                                @error('name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">NIP</label>
                                <div class="ekf-input-group">
                                    <i class="bi bi-credit-card-2-front"></i>
                                    <input type="text" name="nip" value="{{ old('nip') }}" class="form-control @error('nip') is-invalid @enderror" required placeholder="Nomor induk pegawai">
                                </div>
                                @error('nip') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Jabatan</label>
                                <div class="ekf-input-group">
                                    <i class="bi bi-briefcase"></i>
                                    <input type="text" name="position" value="{{ old('position') }}" class="form-control" placeholder="cth. Staff Admin">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">No. HP</label>
                                <div class="ekf-input-group">
                                    <i class="bi bi-telephone"></i>
                                    <input type="text" name="phone" value="{{ old('phone') }}" class="form-control" placeholder="08xx-xxxx-xxxx">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="ekf-section">
                        <div class="ekf-section-title"><span class="num">3</span> Kepegawaian</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Kuota Cuti Tahunan (hari) <small class="text-muted fw-normal">(default 12)</small></label>
                                <div class="ekf-input-group">
                                    <i class="bi bi-calendar-check"></i>
                                    <input type="number" name="leave_quota" min="0" max="365" value="{{ old('leave_quota', 12) }}" class="form-control @error('leave_quota') is-invalid @enderror">
                                </div>
                                @error('leave_quota') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <input type="file" name="photo" id="photoInput" accept="image/*" class="d-none">
                    @error('photo') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror

                    <div class="ekf-actions">
                        <a href="{{ route('admin.employees.index') }}" class="btn btn-outline-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Simpan Karyawan</button>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card p-4 ekf-preview-card">
                    <div class="text-center">
                        <div class="ekf-avatar-wrap">
                            <div class="ekf-avatar" id="avatarPreview"><i class="bi bi-person"></i></div>
                            <label for="photoInput" class="ekf-avatar-upload" title="Unggah foto">
                                <i class="bi bi-camera-fill"></i>
                            </label>
                        </div>
                        <p class="fw-bold mb-1" id="previewName">Karyawan Baru</p>
                        <span class="badge bg-secondary">Foto opsional</span>
                    </div>

                    <hr class="my-3" style="border-color: var(--line);">

                    <div class="ekf-tip">
                        <i class="bi bi-qr-code"></i>
                        <span>QR Code presensi dibuat otomatis begitu data disimpan.</span>
                    </div>
                    <div class="ekf-tip">
                        <i class="bi bi-shield-check"></i>
                        <span>Password ini dipakai karyawan untuk login pertama kali.</span>
                    </div>
                    <div class="ekf-tip">
                        <i class="bi bi-image"></i>
                        <span>Foto profil bisa ditambahkan atau diganti kapan saja lewat halaman edit.</span>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
<script>
    (function () {
        var input = document.getElementById('photoInput');
        var preview = document.getElementById('avatarPreview');
        var nameInput = document.getElementById('nameInput');
        var previewName = document.getElementById('previewName');

        if (input) {
            input.addEventListener('change', function () {
                var file = input.files && input.files[0];
                if (!file) { return; }
                var reader = new FileReader();
                reader.onload = function (e) {
                    preview.innerHTML = '';
                    preview.style.backgroundImage = 'url(' + e.target.result + ')';
                    preview.style.backgroundSize = 'cover';
                    preview.style.backgroundPosition = 'center';
                };
                reader.readAsDataURL(file);
            });
        }

        if (nameInput) {
            nameInput.addEventListener('input', function () {
                previewName.textContent = nameInput.value.trim() || 'Karyawan Baru';
            });
        }
    })();
</script>
@endpush
