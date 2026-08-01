@extends('layouts.app')
@section('title', 'Laporan Absensi')
@section('sidebar')
    @include('layouts._admin-sidebar')
@endsection

@section('content')
    <div class="mb-4">
        <h4 class="mb-1">Laporan Absensi</h4>
        <p class="text-muted mb-0">Filter berdasarkan tanggal, bulan, atau karyawan — lalu export.</p>
    </div>

    <div class="card p-3 mb-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-6 col-md-2">
                <label class="form-label mb-1">Tanggal</label>
                <input type="date" name="date" value="{{ request('date', request('bulan') ? '' : now()->toDateString()) }}" class="form-control">
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label mb-1">Atau Bulan</label>
                <input type="month" name="bulan" value="{{ request('bulan') }}" class="form-control">
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label mb-1">Karyawan</label>
                <select name="employee_id" class="form-select">
                    <option value="">Semua Karyawan</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" @selected(request('employee_id') == $emp->id)>{{ $emp->user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2">
                <button class="btn btn-primary w-100"><i class="bi bi-filter"></i> Filter</button>
            </div>
            <div class="col-12 col-md-3 d-flex gap-2">
                <a href="{{ route('admin.attendances.export-excel', request()->query()) }}" class="btn btn-outline-success flex-fill">
                    <i class="bi bi-file-earmark-excel"></i> Excel
                </a>
                <a href="{{ route('admin.attendances.export-pdf', request()->query()) }}" class="btn btn-outline-danger flex-fill">
                    <i class="bi bi-file-earmark-pdf"></i> PDF
                </a>
            </div>
        </form>
        <small class="text-muted d-block mt-2">Tips: pilih "Bulan" untuk laporan bulanan (mengabaikan filter Tanggal). Tombol Excel/PDF mengikuti filter yang aktif.</small>
    </div>

    <div class="card p-3">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Nama</th>
                        <th>Masuk</th>
                        <th>Pulang</th>
                        <th>Sumber</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $a)
                        <tr>
                            <td>{{ $a->date->format('d-m-Y') }}</td>
                            <td>{{ $a->employee->user->name }}</td>
                            <td>{{ $a->check_in ? \Illuminate\Support\Carbon::parse($a->check_in)->format('H:i') : '-' }}</td>
                            <td>{{ $a->check_out ? \Illuminate\Support\Carbon::parse($a->check_out)->format('H:i') : '-' }}</td>
                            <td>
                                @if($a->is_remote)
                                    <span class="badge bg-info"><i class="bi bi-geo-alt"></i> {{ $a->remote_category_label }}</span>
                                @else
                                    <span class="badge bg-secondary"><i class="bi bi-qr-code-scan"></i> QR</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $a->status_badge }}">
                                    {{ $a->status_label }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4"><i class="bi bi-inbox fs-3 d-block mb-1"></i>Tidak ada data untuk filter ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $attendances->links() }}</div>
    </div>
@endsection
