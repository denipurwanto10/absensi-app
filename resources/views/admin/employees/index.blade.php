@extends('layouts.app')
@section('title', 'Data Karyawan')
@section('sidebar')
    @include('layouts._admin-sidebar')
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-start align-items-md-center mb-4 flex-column flex-md-row gap-2">
        <div>
            <h4 class="mb-1">Data Karyawan</h4>
            <p class="text-muted mb-0">Kelola akun, data, dan QR Code karyawan.</p>
        </div>
        <a href="{{ route('admin.employees.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Tambah Karyawan
        </a>
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
                        <th>Email</th>
                        <th>Kuota Cuti</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $emp)
                        <tr>
                            <td><img src="{{ $emp->photo_url }}" alt="Foto" class="rounded-circle" width="36" height="36" style="object-fit: cover;"></td>
                            <td>{{ $emp->nip }}</td>
                            <td>{{ $emp->user->name }}</td>
                            <td>{{ $emp->position ?? '-' }}</td>
                            <td>{{ $emp->user->email }}</td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $emp->cutiRemaining() }} / {{ $emp->leave_quota }} hari</span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.documents.show', $emp) }}" class="btn btn-sm btn-outline-secondary" title="Dokumen Karyawan">
                                    <i class="bi bi-folder2-open"></i>
                                </a>
                                <a href="{{ route('admin.employees.qrcode', $emp) }}" class="btn btn-sm btn-outline-secondary" title="Lihat QR">
                                    <i class="bi bi-qr-code"></i>
                                </a>
                                <a href="{{ route('admin.employees.edit', $emp) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.employees.destroy', $emp) }}" method="POST" class="d-inline" data-confirm="Data karyawan beserta riwayat terkait akan dihapus dan tidak dapat dikembalikan." data-confirm-type="danger" data-confirm-title="Hapus Karyawan Ini?" data-confirm-ok="Ya, Hapus">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" title="Hapus"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-4"><i class="bi bi-inbox fs-3 d-block mb-1"></i>Belum ada data karyawan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $employees->links() }}</div>
    </div>
@endsection
