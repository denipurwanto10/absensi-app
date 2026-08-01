@extends('layouts.app')
@section('title', 'Dokumen Karyawan')
@section('sidebar')
    @include('layouts._admin-sidebar')
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-start align-items-md-center mb-4 flex-column flex-md-row gap-2">
        <div>
            <h4 class="mb-1">Dokumen Karyawan</h4>
            <p class="text-muted mb-0">Kelola berkas KTP, kontrak kerja, sertifikat, dan dokumen lain per karyawan.</p>
        </div>
        <form action="{{ route('admin.documents.index') }}" method="GET" class="d-flex gap-2">
            <input type="text" name="q" value="{{ $search }}" class="form-control form-control-sm" placeholder="Cari nama / NIP..." style="width: 220px;">
            <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-search"></i></button>
        </form>
    </div>

    <div class="card p-3">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th></th>
                        <th>NIP</th>
                        <th>Nama</th>
                        <th>Jabatan</th>
                        <th>Dokumen Terunggah</th>
                        <th>Kelengkapan</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $emp)
                        @php($__missing = $emp->missing_required_documents)
                        <tr>
                            <td><img src="{{ $emp->photo_url }}" alt="Foto" class="rounded-circle" width="36" height="36" style="object-fit: cover;"></td>
                            <td class="font-mono small">{{ $emp->nip }}</td>
                            <td>{{ $emp->user->name }}</td>
                            <td>{{ $emp->position ?? '-' }}</td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    <i class="bi bi-folder2-open"></i> {{ $emp->documents->count() }} dokumen
                                </span>
                            </td>
                            <td>
                                @if(empty($__missing))
                                    <span class="badge bg-success"><i class="bi bi-check-circle"></i> Lengkap</span>
                                @else
                                    <span class="badge bg-warning" title="Belum ada: {{ implode(', ', $__missing) }}">
                                        <i class="bi bi-exclamation-triangle"></i> Kurang {{ implode(', ', $__missing) }}
                                    </span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.documents.show', $emp) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-folder2"></i> Kelola Dokumen
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-3 d-block mb-1"></i>
                                Tidak ada data karyawan yang cocok.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $employees->links() }}</div>
    </div>
@endsection
