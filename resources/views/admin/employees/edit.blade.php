@extends('layouts.app')
@section('title', 'Edit Karyawan')
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
        width: 112px; height: 112px; border-radius: 50%; background: var(--mist) center/cover no-repeat;
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
    .ekf-quota-box {
        display: flex; gap: .6rem; margin-bottom: .9rem;
    }
    .ekf-quota-stat {
        flex: 1; text-align: center; padding: .7rem .4rem; border-radius: .8rem;
        background: var(--mist); border: 1px solid var(--line);
    }
    .ekf-quota-stat .val { font-family: var(--mono); font-weight: 700; font-size: 1.15rem; color: var(--ink); display: block; }
    .ekf-quota-stat .lbl { font-size: .66rem; color: var(--muted); text-transform: uppercase; letter-spacing: .04em; }
    .ekf-quota-stat.is-remaining .val { color: var(--teal-dark); }
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
        <div class="ekf-header-icon"><i class="bi bi-person-gear"></i></div>
        <div>
            <h4 class="mb-1">Edit Karyawan</h4>
            <p class="text-muted mb-0">Perbarui data, foto, atau password karyawan.</p>
        </div>
    </div>

    <form action="{{ route('admin.employees.update', $employee) }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')
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
                                    <input type="email" name="email" value="{{ old('email', $employee->user->email) }}" class="form-control @error('email') is-invalid @enderror" required>
                                </div>
                                @error('email') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Password <small class="text-muted fw-normal">(kosongkan jika tidak diubah)</small></label>
                                <div class="ekf-input-group">
                                    <i class="bi bi-lock"></i>
                                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="••••••••">
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
                                    <input type="text" name="name" id="nameInput" value="{{ old('name', $employee->user->name) }}" class="form-control @error('name') is-invalid @enderror" required>
                                </div>
                                @error('name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">NIP</label>
                                <div class="ekf-input-group">
                                    <i class="bi bi-credit-card-2-front"></i>
                                    <input type="text" name="nip" value="{{ old('nip', $employee->nip) }}" class="form-control @error('nip') is-invalid @enderror" required>
                                </div>
                                @error('nip') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Jabatan</label>
                                <div class="ekf-input-group">
                                    <i class="bi bi-briefcase"></i>
                                    <input type="text" name="position" value="{{ old('position', $employee->position) }}" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">No. HP</label>
                                <div class="ekf-input-group">
                                    <i class="bi bi-telephone"></i>
                                    <input type="text" name="phone" value="{{ old('phone', $employee->phone) }}" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="ekf-section">
                        <div class="ekf-section-title"><span class="num">3</span> Kepegawaian</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Kuota Cuti Tahunan (hari)</label>
                                <div class="ekf-input-group">
                                    <i class="bi bi-calendar-check"></i>
                                    <input type="number" name="leave_quota" min="0" max="365" value="{{ old('leave_quota', $employee->leave_quota) }}" class="form-control @error('leave_quota') is-invalid @enderror" required>
                                </div>
                                @error('leave_quota') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <input type="file" name="photo" id="photoInput" accept="image/*" class="d-none">
                    @error('photo') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror

                    <div class="ekf-actions">
                        <a href="{{ route('admin.employees.index') }}" class="btn btn-outline-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Perbarui Karyawan</button>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card p-4 ekf-preview-card">
                    <div class="text-center">
                        <div class="ekf-avatar-wrap">
                            <div class="ekf-avatar" id="avatarPreview" style="background-image: url('{{ $employee->photo_url }}');"></div>
                            <label for="photoInput" class="ekf-avatar-upload" title="Ganti foto">
                                <i class="bi bi-camera-fill"></i>
                            </label>
                        </div>
                        <p class="fw-bold mb-1" id="previewName">{{ $employee->user->name }}</p>
                        <span class="badge bg-secondary font-mono">{{ $employee->nip }}</span>
                    </div>

                    <hr class="my-3" style="border-color: var(--line);">

                    <div class="ekf-quota-box">
                        <div class="ekf-quota-stat">
                            <span class="val">{{ $employee->cutiUsed() }}</span>
                            <span class="lbl">Terpakai</span>
                        </div>
                        <div class="ekf-quota-stat is-remaining">
                            <span class="val">{{ $employee->cutiRemaining() }}</span>
                            <span class="lbl">Sisa {{ now()->year }}</span>
                        </div>
                    </div>

                    <div class="ekf-tip">
                        <i class="bi bi-image"></i>
                        <span>Klik ikon kamera untuk mengganti foto profil karyawan.</span>
                    </div>
                    <div class="ekf-tip">
                        <i class="bi bi-shield-lock"></i>
                        <span>Kosongkan password bila tidak ingin mengubahnya.</span>
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
                    preview.style.backgroundImage = 'url(' + e.target.result + ')';
                };
                reader.readAsDataURL(file);
            });
        }

        if (nameInput) {
            nameInput.addEventListener('input', function () {
                previewName.textContent = nameInput.value.trim() || '{{ $employee->user->name }}';
            });
        }
    })();
</script>
@endpush
