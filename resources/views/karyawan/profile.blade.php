@extends('layouts.app')
@section('title', 'Profil Saya')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Profil Saya</h4>
        <div class="d-flex gap-2">
            <a href="{{ route('karyawan.documents.index') }}" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-folder2-open"></i> Dokumen Saya
            </a>
            <a href="{{ route('karyawan.dashboard') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    @if(! $employee)
        <div class="alert alert-warning">Akun kamu belum terhubung ke data karyawan. Hubungi admin.</div>
    @else
        <div class="row g-3">
            <div class="col-12 col-md-5">
                <div class="card p-4 text-center h-100">
                    <img src="{{ $employee->photo_url }}" alt="Foto profil" class="rounded-circle mx-auto"
                         width="120" height="120" style="object-fit: cover;">
                    <h6 class="mt-3 mb-0">{{ $employee->user->name }}</h6>
                    <small class="text-muted mb-3">NIP: {{ $employee->nip }}</small>

                    <form action="{{ route('karyawan.profile.photo') }}" method="POST" enctype="multipart/form-data" class="text-start">
                        @csrf
                        <label class="form-label">Ganti Foto Profil</label>
                        <input type="file" name="photo" accept="image/*" class="form-control @error('photo') is-invalid @enderror" required>
                        @error('photo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <button type="submit" class="btn btn-primary w-100 mt-3">
                            <i class="bi bi-upload"></i> Upload Foto
                        </button>
                    </form>
                </div>
            </div>

            <div class="col-12 col-md-7">
                <div class="card p-4 h-100">
                    <h6 class="mb-3">Ganti Password</h6>
                    <form action="{{ route('karyawan.profile.password') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Password Saat Ini</label>
                            <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" required>
                            @error('current_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password Baru</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Konfirmasi Password Baru</label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-key"></i> Simpan Password Baru
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection
