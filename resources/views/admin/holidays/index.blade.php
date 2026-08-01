@extends('layouts.app')
@section('title', 'Kalender Hari Libur')
@section('sidebar')
    @include('layouts._admin-sidebar')
@endsection

@section('content')
    <div class="mb-4">
        <h4 class="mb-1">Kalender Tanggal Merah</h4>
        <p class="text-muted mb-0">Hari Minggu otomatis libur. Sinkronkan hari libur nasional dari internet, atau tambahkan cuti bersama secara manual.</p>
    </div>

    @include('layouts._holiday-calendar', ['editable' => true, 'indexRoute' => 'admin.holidays.index'])
@endsection
