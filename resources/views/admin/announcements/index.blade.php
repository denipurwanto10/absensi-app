@extends('layouts.app')
@section('title', 'Pengumuman')
@section('sidebar')
    @include('layouts._admin-sidebar')
@endsection

@section('content')
    <div class="mb-4">
        <h4 class="mb-1">Pengumuman</h4>
        <p class="text-muted mb-0">Broadcast informasi ke semua karyawan. Muncul di lonceng notifikasi mereka.</p>
    </div>

    <div class="row g-3">
        <div class="col-12 col-lg-5">
            <div class="card p-4">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $e)
                                <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <h6 class="mb-3"><i class="bi bi-megaphone"></i> Buat Pengumuman Baru</h6>
                <form action="{{ route('admin.announcements.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Judul</label>
                        <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Isi Pengumuman</label>
                        <textarea name="body" class="form-control" rows="4" required>{{ old('body') }}</textarea>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="is_pinned" id="is_pinned" value="1" {{ old('is_pinned') ? 'checked' : '' }}>
                        <label class="form-check-label small" for="is_pinned">Sematkan di atas (penting)</label>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-send"></i> Publikasikan
                    </button>
                </form>
            </div>
        </div>

        <div class="col-12 col-lg-7">
            <div class="d-flex flex-column gap-3">
                @forelse($announcements as $a)
                    <div class="card p-3">
                        <div class="d-flex justify-content-between align-items-start gap-2">
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    @if($a->is_pinned)
                                        <span class="badge" style="background:var(--amber); color:var(--ink);"><i class="bi bi-pin-angle-fill"></i> Disematkan</span>
                                    @endif
                                    <h6 class="mb-0">{{ $a->title }}</h6>
                                </div>
                                <p class="mb-1 small" style="white-space: pre-line;">{{ $a->body }}</p>
                                <small class="text-muted">
                                    Oleh {{ $a->author->name ?? 'Admin' }} &middot; {{ $a->created_at->translatedFormat('d F Y, H:i') }}
                                </small>
                            </div>
                            <form action="{{ route('admin.announcements.destroy', $a) }}" method="POST" data-confirm="Pengumuman ini akan dihapus dan tidak akan tampil lagi untuk karyawan." data-confirm-type="danger" data-confirm-title="Hapus Pengumuman Ini?" data-confirm-ok="Ya, Hapus">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="card p-4 text-center text-muted">
                        <i class="bi bi-megaphone fs-3 d-block mb-1"></i>
                        Belum ada pengumuman.
                    </div>
                @endforelse
                <div>{{ $announcements->links() }}</div>
            </div>
        </div>
    </div>
@endsection
