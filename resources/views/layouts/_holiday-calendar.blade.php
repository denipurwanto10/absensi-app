{{--
    Partial kalender bulanan "tanggal merah". Dipakai bareng oleh
    admin.holidays.index & karyawan.holidays.index.

    Variabel yang dibutuhkan dari parent view:
    - $cursor            : Carbon, tanggal 1 di bulan yang sedang ditampilkan
    - $holidaysInMonth    : Collection<Holiday> keyed by 'Y-m-d'
    - $year, $month       : int
    - $editable           : bool (true = admin, bisa tambah/hapus/sinkron)
    - $indexRoute         : nama route index (untuk navigasi bulan)
--}}
@php
    $__daysInMonth = $cursor->daysInMonth;
    $__startOffset = $cursor->copy()->startOfMonth()->dayOfWeek; // 0 = Minggu
    $__prev = $cursor->copy()->subMonth();
    $__next = $cursor->copy()->addMonth();
    $__today = now()->toDateString();
@endphp

<style>
    .cal-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 6px; }
    .cal-dow { text-align: center; font-size: .72rem; text-transform: uppercase; letter-spacing: .05em; color: var(--muted); font-family: var(--mono); padding-bottom: 2px; }
    .cal-cell {
        aspect-ratio: 1 / 1; border-radius: .6rem; border: 1px solid var(--line); background: #fff;
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        font-size: .85rem; position: relative; padding: 2px;
    }
    .cal-cell.is-empty { background: transparent; border: none; }
    .cal-cell.is-today { border: 2px solid var(--teal); font-weight: 700; }
    .cal-cell.is-red { background: var(--coral-soft); color: var(--coral); font-weight: 600; border-color: transparent; }
    .cal-cell.is-cuti-bersama { background: var(--amber-soft); color: var(--amber-ink); font-weight: 600; border-color: transparent; }
    .cal-cell .cal-holiday-name {
        font-size: .55rem; line-height: 1.05; text-align: center; margin-top: 1px;
        display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
    }
</style>

<div class="card p-3 mb-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route($indexRoute, ['bulan' => $__prev->month, 'tahun' => $__prev->year]) }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-chevron-left"></i>
        </a>
        <h6 class="mb-0">{{ $cursor->translatedFormat('F Y') }}</h6>
        <a href="{{ route($indexRoute, ['bulan' => $__next->month, 'tahun' => $__next->year]) }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-chevron-right"></i>
        </a>
    </div>

    <div class="cal-grid mb-2">
        @foreach(['Min', 'Sen', 'Sel', 'Rab', 'Kam', "Jum'at", 'Sab'] as $__dow)
            <div class="cal-dow">{{ $__dow }}</div>
        @endforeach

        @for($__i = 0; $__i < $__startOffset; $__i++)
            <div class="cal-cell is-empty"></div>
        @endfor

        @for($__d = 1; $__d <= $__daysInMonth; $__d++)
            @php
                $__date = $cursor->copy()->day($__d);
                $__dateStr = $__date->toDateString();
                $__holiday = $holidaysInMonth->get($__dateStr);
                $__isSunday = $__date->isSunday();
                $__cellClass = 'cal-cell';
                if ($__dateStr === $__today) { $__cellClass .= ' is-today'; }
                if ($__holiday) {
                    $__cellClass .= $__holiday->is_national ? ' is-red' : ' is-cuti-bersama';
                } elseif ($__isSunday) {
                    $__cellClass .= ' is-red';
                }
            @endphp
            <div class="{{ $__cellClass }}" title="{{ $__holiday->name ?? ($__isSunday ? 'Libur mingguan' : '') }}">
                <span>{{ $__d }}</span>
                @if($__holiday)
                    <span class="cal-holiday-name">{{ $__holiday->name }}</span>
                @endif
            </div>
        @endfor
    </div>

    <div class="d-flex flex-wrap gap-3 small text-muted mt-1">
        <span><span class="d-inline-block rounded-1" style="width:10px;height:10px;background:var(--coral-soft);border:1px solid var(--coral);"></span> Libur nasional / Minggu</span>
        <span><span class="d-inline-block rounded-1" style="width:10px;height:10px;background:var(--amber-soft);border:1px solid var(--amber);"></span> Cuti bersama / manual</span>
    </div>
</div>

@if($editable)
    <div class="row g-3 mb-3">
        <div class="col-12 col-md-6">
            <div class="card p-3 h-100">
                <h6 class="mb-2"><i class="bi bi-cloud-arrow-down"></i> Sinkronkan Hari Libur Nasional</h6>
                <p class="text-muted small mb-3">Ambil otomatis daftar tanggal merah nasional untuk satu tahun dari layanan kalender publik. Aman dijalankan berulang — data lama akan diperbarui, bukan diduplikat.</p>
                <form action="{{ route('admin.holidays.sync') }}" method="POST" class="d-flex gap-2">
                    @csrf
                    <select name="tahun" class="form-select" style="max-width: 140px;">
                        @foreach(range(now()->year - 1, now()->year + 2) as $__y)
                            <option value="{{ $__y }}" {{ $__y === $year ? 'selected' : '' }}>{{ $__y }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-arrow-repeat"></i> Sinkronkan
                    </button>
                </form>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="card p-3 h-100">
                <h6 class="mb-2"><i class="bi bi-calendar-plus"></i> Tambah Cuti Bersama / Libur Manual</h6>
                <form action="{{ route('admin.holidays.store') }}" method="POST" class="row g-2">
                    @csrf
                    <div class="col-5">
                        <input type="date" name="date" class="form-control" required>
                    </div>
                    <div class="col-5">
                        <input type="text" name="name" class="form-control" placeholder="Nama libur" required>
                    </div>
                    <div class="col-2">
                        <button type="submit" class="btn btn-outline-primary w-100"><i class="bi bi-plus-lg"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

<div class="card p-3">
    <h6 class="mb-3">Daftar Hari Libur Tahun {{ $year }}</h6>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Nama</th>
                    <th>Jenis</th>
                    @if($editable)<th class="text-end">Aksi</th>@endif
                </tr>
            </thead>
            <tbody>
                @forelse($holidaysThisYear as $__h)
                    <tr>
                        <td class="font-mono">{{ $__h->date->translatedFormat('d M Y (l)') }}</td>
                        <td>{{ $__h->name }}</td>
                        <td>
                            @if($__h->is_national)
                                <span class="badge" style="background:var(--coral-soft); color:var(--coral);">Libur Nasional</span>
                            @else
                                <span class="badge" style="background:var(--amber-soft); color:var(--amber-ink);">Cuti Bersama</span>
                            @endif
                        </td>
                        @if($editable)
                            <td class="text-end">
                                <form action="{{ route('admin.holidays.destroy', $__h) }}" method="POST" class="d-inline" data-confirm="Hari libur ini akan dihapus dari kalender." data-confirm-type="danger" data-confirm-title="Hapus Hari Libur Ini?" data-confirm-ok="Ya, Hapus">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $editable ? 4 : 3 }}" class="text-center text-muted py-4">
                            <i class="bi bi-calendar-x fs-3 d-block mb-1"></i>
                            Belum ada data hari libur untuk tahun ini.
                            @if($editable) Coba klik "Sinkronkan". @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
