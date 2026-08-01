@php
    $histories = $model->statusHistories;
    $isFinal = in_array($model->status, ['approved', 'rejected']);
@endphp
<ul class="status-timeline">
    @foreach($histories as $h)
        <li class="is-{{ $h->status }}">
            <span class="tl-dot"><i class="bi {{ $h->status_icon }}"></i></span>
            <div class="tl-title">{{ $h->status_label }}</div>
            <div class="tl-meta">
                <i class="bi bi-clock"></i> {{ $h->created_at->translatedFormat('d M Y, H:i') }}
                @if($h->changer) &middot; {{ $h->changer->name }} @endif
            </div>
            @if($h->note)
                <div class="tl-note">{{ $h->note }}</div>
            @endif
        </li>
    @endforeach

    @unless($isFinal)
        @if($model->status === 'pending')
            <li class="is-future">
                <span class="tl-dot"><i class="bi bi-hourglass-split"></i></span>
                <div class="tl-title">Diproses</div>
                <div class="tl-meta">Menunggu ditinjau admin</div>
            </li>
        @endif
        <li class="is-future">
            <span class="tl-dot"><i class="bi bi-flag"></i></span>
            <div class="tl-title">Disetujui / Ditolak</div>
            <div class="tl-meta">Menunggu keputusan akhir admin</div>
        </li>
    @endunless
</ul>
