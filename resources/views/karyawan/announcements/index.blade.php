@extends('layouts.app')
@section('title', 'Pengumuman')

@section('content')
    <div class="mb-4">
        <h4 class="mb-1">Pengumuman</h4>
        <p class="text-muted mb-0">Informasi terbaru dari admin/perusahaan.</p>
    </div>

    <div class="d-flex flex-column gap-3">
        @forelse($announcements as $a)
            <div class="card p-3">
                <div class="d-flex align-items-center gap-2 mb-1">
                    @if($a->is_pinned)
                        <span class="badge" style="background:var(--amber); color:var(--ink);"><i class="bi bi-pin-angle-fill"></i> Penting</span>
                    @endif
                    <h6 class="mb-0">{{ $a->title }}</h6>
                </div>
                <p class="mb-1 small" style="white-space: pre-line;">{{ $a->body }}</p>
                <small class="text-muted">
                    Oleh {{ $a->author->name ?? 'Admin' }} &middot; {{ $a->created_at->translatedFormat('d F Y, H:i') }}
                </small>
            </div>
        @empty
            <div class="card p-4 text-center text-muted">
                <i class="bi bi-megaphone fs-3 d-block mb-1"></i>
                Belum ada pengumuman.
            </div>
        @endforelse
        <div>{{ $announcements->links() }}</div>
    </div>
@endsection
