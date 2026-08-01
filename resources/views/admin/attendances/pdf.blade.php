<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Absensi</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #222; }
        h2 { margin-bottom: 0; }
        p.subtitle { margin-top: 4px; color: #555; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { border: 1px solid #999; padding: 6px 8px; text-align: left; }
        th { background-color: #eef2ff; }
        .badge { padding: 2px 6px; border-radius: 4px; font-size: 10px; color: #fff; }
        .badge-hadir { background-color: #198754; }
        .badge-telat { background-color: #ffc107; color: #222; }
        .badge-alpha { background-color: #dc3545; }
        .badge-izin, .badge-sakit, .badge-cuti { background-color: #2F5FA3; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <h2>Laporan Absensi</h2>
    <p class="subtitle">Periode: {{ $periode }} &middot; Dicetak: {{ now()->translatedFormat('d F Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>NIP</th>
                <th>Nama</th>
                <th>Jabatan</th>
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
                    <td>{{ $a->employee->nip }}</td>
                    <td>{{ $a->employee->user->name }}</td>
                    <td>{{ $a->employee->position ?? '-' }}</td>
                    <td>{{ $a->check_in ? \Illuminate\Support\Carbon::parse($a->check_in)->format('H:i') : '-' }}</td>
                    <td>{{ $a->check_out ? \Illuminate\Support\Carbon::parse($a->check_out)->format('H:i') : '-' }}</td>
                    <td>{{ $a->is_remote ? $a->remote_category_label.' (Luar Kantor)' : 'QR (Kantor)' }}</td>
                    <td><span class="badge badge-{{ $a->status }}">{{ ucfirst($a->status) }}</span></td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center">Tidak ada data untuk periode ini.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
