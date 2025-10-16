@extends('layouts/contentNavbarLayout')

@section('title', 'Manajemen - Lokasi')

@section('page-style')
    {{-- Custom CSS untuk peta, marker Boxicons, dan circle radius --}}
    <style>
        /* Force override untuk peta */
        #map {
            height: 400px !important;
            width: 100% !important;
            min-height: 400px !important;
            border: 1px solid #ddd !important;
            border-radius: 0.375rem !important;
            overflow: hidden !important;
            position: relative !important;
            z-index: 1 !important;
            display: block !important;
            background-color: #f8f9fa !important;
        }

        /* Leaflet container internal */
        #map .leaflet-container {
            height: 100% !important;
            width: 100% !important;
            border-radius: 0.375rem !important;
            font-family: 'Public Sans', sans-serif !important;
        }

        /* Card body */
        .card-body {
            overflow: visible !important;
            position: relative !important;
            padding: 1.5rem !important;
        }

        /* Row fix */
        .row {
            --bs-gutter-x: 1.5rem !important;
            margin-bottom: 1rem !important;
        }

        /* Styling geocoder search box */
        .leaflet-control-geocoder {
            margin-top: 10px !important;
            font-size: 14px !important;
            z-index: 1000 !important;
        }

        .leaflet-control-geocoder .leaflet-control-geocoder-form {
            border-radius: 0.375rem !important;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
        }

        .leaflet-control-geocoder .leaflet-control-geocoder-form input {
            border: 1px solid #d0d7de !important;
            border-radius: 0.375rem !important;
            padding: 0.375rem 0.75rem !important;
            width: 250px !important;
            background-color: #fff !important;
            color: #697a8d !important;
            font-family: 'Public Sans', sans-serif !important;
        }

        .leaflet-control-geocoder .leaflet-control-geocoder-form input::placeholder {
            color: #a1acb8 !important;
            opacity: 1 !important;
        }

        .leaflet-control-geocoder .leaflet-control-geocoder-results {
            border: 1px solid #d0d7de !important;
            border-top: none !important;
            border-radius: 0 0 0.375rem 0.375rem !important;
            max-height: 200px !important;
            overflow-y: auto !important;
            background-color: #fff !important;
            box-shadow: 0 0.25rem 0.375rem rgba(161, 172, 184, 0.15) !important;
        }

        .leaflet-control-geocoder .leaflet-control-geocoder-result {
            color: #697a8d !important;
            padding: 0.5rem !important;
            border-bottom: 1px solid #e2e8f0 !important;
        }

        .leaflet-control-geocoder .leaflet-control-geocoder-result:hover {
            background-color: #f8f9fa !important;
        }

        /* CUSTOM MARKER DENGAN BOXICONS */
        .custom-marker {
            background: transparent !important;
            border: none !important;
            cursor: pointer !important;
            z-index: 1 !important;
        }

        .custom-marker i {
            font-size: 24px !important;
            color: #dc3545 !important;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5) !important;
            transition: transform 0.2s ease !important;
            display: block !important;
            margin: 0 !important;
            line-height: 1 !important;
        }

        .custom-marker i:hover {
            transform: scale(1.1) !important;
        }

        /* STYLING UNTUK CIRCLE RADIUS - Warna biru semi-transparan, match Sneat */
        .custom-circle {
            stroke: #0d6efd !important;
            /* Border biru (Bootstrap primary) */
            fill: #0d6efd !important;
            /* Isi biru */
            fill-opacity: 0.2 !important;
            /* Transparan 20% */
            stroke-width: 2 !important;
            /* Lebar border */
            weight: 2 !important;
            opacity: 0.8 !important;
            z-index: 1 !important;
        }

        /* Textbox radius styling */
        #radius {
            border: 1px solid #d0d7de !important;
            border-radius: 0.375rem !important;
            padding: 0.375rem 0.75rem !important;
            font-family: 'Public Sans', sans-serif !important;
            color: #697a8d !important;
        }

        #radius::placeholder {
            color: #a1acb8 !important;
        }

        /* Responsif untuk mobile */
        @media (max-width: 768px) {
            #map {
                height: 300px !important;
            }

            .leaflet-control-geocoder .leaflet-control-geocoder-form input {
                width: 100% !important;
            }

            .custom-marker i {
                font-size: 20px !important;
            }

            #radius {
                margin-bottom: 1rem !important;
            }
        }
    </style>
@endsection

@section('content')
    <div class="row g-0">
        <div class="col-md-12">
            <div class="card">
                <h5 class="card-header"><i class="bx bx-lg bx-map-alt"></i> Manajemen / Lokasi
                </h5>

                @if (session('success'))
                    <div class="alert alert-success d-flex alert-dismissible mx-5" role="alert">
                        <span class="alert-icon rounded-circle">
                            <i class="icon-base bx bxs-check-circle icon-sm"></i>
                        </span>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                        </button>
                    </div>
                @endif

                <div class="card-body col-12 mx-auto">
                    <form id="mapForm" method="post" action="{{ route('config-lokasi.update', 'lokasi') }}">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-4 mb-3"> {{-- Ubah ke col-md-4 untuk fit 3 input --}}
                                <label for="latitude" class="form-label">Latitude</label>
                                <input type="text" class="form-control" id="latitude" name="latitude"
                                    placeholder="Contoh: -0.900270" readonly required>
                                <div class="form-text">Koordinat latitude akan terisi otomatis saat klik peta atau search.
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="longitude" class="form-label">Longitude</label>
                                <input type="text" class="form-control" id="longitude" name="longitude"
                                    placeholder="Contoh: 119.8890" readonly required>
                                <div class="form-text">Koordinat longitude akan terisi otomatis saat klik peta atau search.
                                </div>
                            </div>
                            <div class="col-md-4 mb-3"> {{-- Textbox baru untuk radius --}}
                                <label for="radius" class="form-label">Radius (meter)</label>
                                <input type="number" class="form-control" id="radius" name="radius" placeholder="100"
                                    min="10" max="5000" step="10" value="100" required>
                                <div class="form-text">Masukkan radius zona (min 10m, max 5000m). Lingkaran akan muncul di
                                    peta secara real-time.</div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Peta Interaktif dengan Search & Radius</label>
                            <div id="map"
                                style="height: 400px !important; width: 100% !important; border: 1px solid #ddd; border-radius: 0.375rem; overflow: hidden; position: relative; z-index: 1; background-color: #f8f9fa;">
                            </div>
                            <div class="form-text">
                                <strong>Cara Pakai:</strong>
                                1. Gunakan search box di atas peta untuk cari lokasi (contoh: "Monas Jakarta").
                                2. Atau klik langsung pada peta untuk pilih titik manual.
                                3. Atur radius di textbox (misalnya 200m) – lingkaran biru akan muncul otomatis di sekitar
                                marker.
                                Koordinat dan radius akan tersimpan saat submit.
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary">Simpan Koordinat & Radius</button>
                            {{-- <a href="{{ url()->previous() }}" class="btn btn-secondary">Kembali</a> --}}
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- Leaflet core --}}
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    {{-- Leaflet Geocoder --}}
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Custom Boxicons icon (sama seperti sebelumnya)
            var customIcon = L.divIcon({
                className: 'custom-marker',
                html: '<i class="bx bx-map"></i>',
                iconSize: [50, 50],
                iconAnchor: [15, 30],
                popupAnchor: [0, -30]
            });
            var latitude, longitude, radius;
            // Ambil data dari Parameter Lokasi
            // Ambil data lokasi via jQuery AJAX (synchronous agar nilai tersedia sebelum inisialisasi peta)
            // Ganti '1' pada route jika perlu dengan ID yang sesuai
            var url = "{{ route('config-lokasi.show', 'lokasi') }}";
            try {
                $.ajax({
                    url: url,
                    method: 'GET',
                    dataType: 'json',
                    async: false, // synchronous to keep existing flow (kurangi jika ingin async)
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(data) {
                        // Asumsikan respon JSON berisi { latitude: ..., longitude: ..., radius: ... }
                        latitude = data.latitude !== undefined && data.latitude !== null ? parseFloat(
                            data.latitude) : null;
                        longitude = data.longitude !== undefined && data.longitude !== null ?
                            parseFloat(data.longitude) : null;
                        radius = data.radius !== undefined && data.radius !== null ? parseInt(data
                            .radius) : null;

                        if (!isNaN(latitude)) document.getElementById('latitude').value = latitude
                            .toFixed(6);
                        if (!isNaN(longitude)) document.getElementById('longitude').value = longitude
                            .toFixed(6);
                        if (!isNaN(radius)) document.getElementById('radius').value = radius;

                        console.log('Data lokasi berhasil diambil via jQuery AJAX:', {
                            latitude,
                            longitude,
                            radius
                        });

                        document.getElementById('latitude').value = latitude !== null ? latitude
                            .toFixed(6) : '';
                        document.getElementById('longitude').value = longitude !== null ? longitude
                            .toFixed(6) : '';
                        document.getElementById('radius').value = radius !== null ? radius : 100;
                    },
                    error: function(xhr, status, error) {
                        console.warn('Gagal mengambil data lokasi. Status:', status, 'HTTP:', xhr
                            .status, 'Error:', error);
                    }
                });
            } catch (e) {
                console.error('Error ketika mengambil data lokasi via jQuery AJAX:', e);
            }

            var longitudeStr = (typeof longitude !== 'undefined' && longitude !== null) ? String(longitude) : (
                document.getElementById('longitude').value || '');
            longitudeStr = longitudeStr.trim();

            var splitLongtitude = '';
            var lastTwoLongtitude = '';

            if (longitudeStr.length >= 2) {
                splitLongtitude = longitudeStr.slice(0, -2);
                lastTwoLongtitude = longitudeStr.slice(-2);
            } else {
                // jika kurang dari 2 karakter, simpan seluruhnya di lastTwoLongtitude
                lastTwoLongtitude = longitudeStr;
            }

            // Inisialisasi peta
            var map = L.map('map').setView([latitude, splitLongtitude], lastTwoLongtitude);

            // Tile layer OSM (sama seperti sebelumnya)
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 19
            }).addTo(map);

            // Variabel global untuk marker dan circle
            window.currentMarker = null;
            window.currentCircle = null;

            // Fungsi untuk update radius (dipanggil saat textbox berubah atau marker baru)
            function updateRadius(lat, lng) {
                var radiusValue = parseInt(document.getElementById('radius').value) ||
                    radius; // Default 100m jika kosong
                radiusValue = Math.max(10, Math.min(5000, radiusValue)); // Clamp antara 10-5000m

                // Hapus circle lama jika ada
                if (window.currentCircle) {
                    map.removeLayer(window.currentCircle);
                }

                // Tambah circle baru di sekitar marker
                window.currentCircle = L.circle([lat, lng], {
                    radius: radiusValue, // Radius dalam meter
                    className: 'custom-circle' // Class untuk styling CSS
                }).addTo(map);

                // Update popup marker untuk tampilkan radius
                if (window.currentMarker) {
                    window.currentMarker.bindPopup('Titik: ' + lat.toFixed(6) + ', ' + lng.toFixed(6) +
                        '<br>Radius: ' + radiusValue + 'm');
                }

                console.log('Radius diupdate:', radiusValue + 'm di lokasi', lat, lng);
            }

            // Event listener untuk textbox radius (real-time update)
            document.getElementById('radius').addEventListener('input', function() {
                if (window.currentMarker) {
                    var lat = parseFloat(document.getElementById('latitude').value);
                    var lng = parseFloat(document.getElementById('longitude').value);
                    if (!isNaN(lat) && !isNaN(lng)) {
                        updateRadius(lat, lng);
                    }
                }
            });

            // Refresh ukuran peta
            setTimeout(() => {
                map.invalidateSize();
                var mapDiv = document.getElementById('map');
                console.log('Peta di-refresh. Ukuran:', mapDiv.offsetWidth + 'x' + mapDiv.offsetHeight);
            }, 500);

            // Geocoder (update dengan radius)
            var geocoder = L.Control.geocoder({
                    defaultMarkGeocode: false,
                    placeholder: 'Cari lokasi... (contoh: Monas Jakarta)',
                    errorMessage: 'Lokasi tidak ditemukan!',
                    geocoder: L.Control.Geocoder.nominatim()
                })
                .on('markgeocode', function(e) {
                    var latlng = e.geocode.center;
                    map.setView(latlng, 16);

                    // Hapus marker dan circle lama
                    if (window.currentMarker) {
                        map.removeLayer(window.currentMarker);
                    }
                    if (window.currentCircle) {
                        map.removeLayer(window.currentCircle);
                    }

                    // Tambah marker baru
                    window.currentMarker = L.marker(latlng, {
                            icon: customIcon
                        }).addTo(map)
                        .bindPopup(e.geocode.name + '<br>Lat: ' + latlng.lat.toFixed(6) + '<br>Lng: ' + latlng
                            .lng.toFixed(6) + '<br>Radius: ' + document.getElementById('radius').value + 'm')
                        .openPopup();

                    // Update textbox dan radius
                    document.getElementById('latitude').value = latlng.lat.toFixed(6);
                    document.getElementById('longitude').value = latlng.lng.toFixed(6);
                    updateRadius(latlng.lat, latlng.lng);

                    console.log('Search result dengan radius:', e.geocode.name, latlng);
                })
                .addTo(map);

            // Event klik (update dengan radius)
            map.on('click', function(e) {
                var lat = e.latlng.lat.toFixed(6);
                var lng = e.latlng.lng.toFixed(6);

                document.getElementById('latitude').value = lat;
                document.getElementById('longitude').value = lng;

                // Hapus marker dan circle lama
                if (window.currentMarker) {
                    map.removeLayer(window.currentMarker);
                }
                if (window.currentCircle) {
                    map.removeLayer(window.currentCircle);
                }

                // Tambah marker baru
                window.currentMarker = L.marker([lat, lng], {
                        icon: customIcon
                    }).addTo(map)
                    .bindPopup('Titik Manual: ' + lat + ', ' + lng + '<br>Radius: ' + document
                        .getElementById('radius').value + 'm')
                    .openPopup();

                // Update radius
                updateRadius(parseFloat(lat), parseFloat(lng));

                console.log('Klik manual dengan radius:', lat, lng);
            });

            // --- Inisialisasi default dari input / server ---
            // Jika server mengirimkan nilai lewat blade/old, gunakan itu. Jika tidak, pakai fallback.
            var initialLat = parseFloat(document.getElementById('latitude').value) || latitude;
            var initialLng = parseFloat(document.getElementById('longitude').value) || longitude;
            var initialRadius = parseInt(document.getElementById('radius').value) || radius;

            // Set inputs jika masih kosong
            document.getElementById('latitude').value = initialLat.toFixed(6);
            document.getElementById('longitude').value = initialLng.toFixed(6);
            document.getElementById('radius').value = initialRadius;

            // Buat marker dan circle awal agar langsung terlihat ketika halaman dibuka
            if (window.currentMarker) {
                map.removeLayer(window.currentMarker);
            }
            if (window.currentCircle) {
                map.removeLayer(window.currentCircle);
            }

            window.currentMarker = L.marker([initialLat, initialLng], {
                    icon: customIcon
                })
                .addTo(map)
                .bindPopup('Lokasi Default: ' + initialLat.toFixed(6) + ', ' + initialLng.toFixed(6) +
                    '<br>Radius: ' + initialRadius + 'm');

            updateRadius(initialLat, initialLng);

            // Pusatkan peta ke koordinat awal
            map.setView([initialLat, initialLng], 14);

            console.log('Peta siap dengan fitur radius! Lokasi awal ditampilkan.');
        });
    </script>
@endpush
