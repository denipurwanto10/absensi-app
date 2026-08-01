@extends('layouts.app')
@section('title', 'Kalender Hari Libur')

@section('content')
    <div class="mb-4">
        <h4 class="mb-1">Kalender Hari Libur</h4>
        <p class="text-muted mb-0">Tanggal merah nasional & cuti bersama. Hari Minggu otomatis libur.</p>
    </div>

    @include('layouts._holiday-calendar', ['editable' => false, 'indexRoute' => 'karyawan.holidays.index'])
@endsection
