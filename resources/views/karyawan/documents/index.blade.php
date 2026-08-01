@extends('layouts.app')
@section('title', 'Dokumen Saya')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Dokumen Saya</h4>
        <a href="{{ route('karyawan.profile.edit') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    @if(! $employee)
        <div class="alert alert-warning">Akun kamu belum terhubung ke data karyawan. Hubungi admin.</div>
    @else
        <p class="text-muted mb-4">Unggah KTP, kontrak kerja, sertifikat, atau dokumen kepegawaian lain. Admin dapat melihat dokumen yang kamu unggah di sini.</p>

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card p-4">
                    <h6 class="mb-3"><i class="bi bi-upload"></i> Unggah Dokumen</h6>
                    <form action="{{ route('karyawan.documents.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Jenis Dokumen</label>
                            <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                                <option value="ktp">KTP</option>
                                <option value="kontrak_kerja">Kontrak Kerja</option>
                                <option value="sertifikat">Sertifikat</option>
                                <option value="lainnya">Lainnya</option>
                            </select>
                            @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Dokumen</label>
                            <input type="text" name="title" value="{{ old('title') }}" class="form-control @error('title') is-invalid @enderror" placeholder="mis. KTP, Sertifikat K3" required>
                            @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Berkas <small class="text-muted fw-normal">(JPG/PNG/PDF, maks 5MB)</small></label>
                            <input type="file" name="file" accept=".jpg,.jpeg,.png,.pdf" class="form-control @error('file') is-invalid @enderror" required>
                            @error('file') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Catatan <small class="text-muted fw-normal">(opsional)</small></label>
                            <textarea name="note" class="form-control" rows="2">{{ old('note') }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-upload"></i> Unggah</button>
                    </form>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="row g-3">
                    @forelse($documents as $doc)
                        <div class="col-md-6">
                            <div class="doc-card">
                                <div class="d-flex align-items-start gap-2">
                                    <div class="doc-ico"><i class="bi {{ $doc->type_icon }}"></i></div>
                                    <div class="flex-grow-1">
                                        <div class="fw-bold">{{ $doc->title }}</div>
                                        <span class="badge bg-light text-dark border">{{ $doc->type_label }}</span>
                                    </div>
                                </div>
                                @if($doc->note)
                                    <p class="text-muted small mb-0">{{ $doc->note }}</p>
                                @endif
                                <div class="text-muted small">
                                    <i class="bi bi-calendar3"></i> Diunggah {{ $doc->created_at->format('d-m-Y') }}
                                </div>
                                <div class="d-flex gap-2 mt-auto pt-2">
                                    <a href="{{ $doc->file_url }}" target="_blank" class="btn btn-sm btn-outline-primary flex-grow-1">
                                        <i class="bi bi-eye"></i> Lihat
                                    </a>
                                    <form action="{{ route('karyawan.documents.destroy', $doc) }}" method="POST" data-confirm="Dokumen ini akan dihapus permanen." data-confirm-type="danger" data-confirm-title="Hapus Dokumen Ini?" data-confirm-ok="Ya, Hapus">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="card p-5 text-center text-muted">
                                <i class="bi bi-folder2-open fs-2 d-block mb-2"></i>
                                Kamu belum mengunggah dokumen apa pun.
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    @endif
@endsection
