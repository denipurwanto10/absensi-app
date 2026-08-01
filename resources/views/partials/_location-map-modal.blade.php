{{-- Modal peta lokasi yang dipakai ulang (koreksi absensi, izin, absen luar kantor).
     Panggil window.showLocationMap(lat, lng, title, subtitle) dari tombol "Lihat Lokasi". --}}
<div class="modal fade" id="locationMapModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="locationMapModalTitle"><i class="bi bi-geo-alt-fill me-1"></i> Titik Lokasi</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body p-0">
                <div id="locationMapContainer" style="height: 360px; width: 100%; background: var(--mist);"></div>
                <div class="p-3 small d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <span class="font-mono text-muted" id="locationMapCoords">-</span>
                    <a href="#" target="_blank" rel="noopener" id="locationMapExternalLink" class="text-decoration-none fw-semibold">
                        <i class="bi bi-box-arrow-up-right"></i> Buka di Google Maps
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@once
    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css" />
        <style>
            #locationMapContainer { border-radius: 0 0 1rem 1rem; }
            .leaflet-pane, .leaflet-control { z-index: 5; }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js"></script>
        <script>
            (function () {
                let map = null;
                let marker = null;

                window.showLocationMap = function (lat, lng, title, subtitle) {
                    lat = parseFloat(lat);
                    lng = parseFloat(lng);

                    document.getElementById('locationMapModalTitle').innerHTML =
                        '<i class="bi bi-geo-alt-fill me-1"></i> ' + (title || 'Titik Lokasi');
                    document.getElementById('locationMapCoords').textContent =
                        (subtitle ? subtitle + ' — ' : '') + lat.toFixed(6) + ', ' + lng.toFixed(6);
                    document.getElementById('locationMapExternalLink').href =
                        'https://www.google.com/maps?q=' + lat + ',' + lng;

                    const modalEl = document.getElementById('locationMapModal');
                    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);

                    modalEl.addEventListener('shown.bs.modal', function handler() {
                        if (!map) {
                            map = L.map('locationMapContainer').setView([lat, lng], 16);
                            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                                maxZoom: 19,
                            }).addTo(map);
                            marker = L.marker([lat, lng]).addTo(map);
                        } else {
                            map.setView([lat, lng], 16);
                            marker.setLatLng([lat, lng]);
                        }
                        setTimeout(function () { map.invalidateSize(); }, 150);
                        modalEl.removeEventListener('shown.bs.modal', handler);
                    });

                    modal.show();
                };
            })();
        </script>
    @endpush
@endonce
