{{-- Butuh variabel: $id (unik), $title, $model (punya relasi statusHistories) --}}
<div class="modal fade" id="{{ $id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title mb-0"><i class="bi bi-clock-history"></i> {{ $title }}</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                @include('partials._status-timeline', ['model' => $model])
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
