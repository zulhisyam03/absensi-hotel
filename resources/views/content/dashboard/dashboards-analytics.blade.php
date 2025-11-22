@extends('layouts/contentNavbarLayout')

@section('title', 'Dashboard')

@section('vendor-style')
    @vite('resources/assets/vendor/libs/apex-charts/apex-charts.scss')
@endsection

@section('vendor-script')
    @vite('resources/assets/vendor/libs/apex-charts/apexcharts.js')
@endsection

@section('page-script')
    @vite('resources/assets/js/dashboards-analytics.js')
    @vite(['resources/assets/js/ui-modals.js'])
@endsection

@section('content')
    <div class="row">
        <!-- Attendence -->
        <div class="col-12 col-xxl-8 order-2 order-md-3 order-xxl-2 mb-6">
            <div class="card">
                <div class="row row-bordered g-0">
                    <div class="col-lg-12">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <div class="card-title mb-0">
                                <h5 class="m-0 me-2"><i class="bx bx-lg bx-history"></i> History</h5>
                            </div>
                        </div>
                        @if (strtolower(Auth::user()->pegawai->jabatan) == 'hr' || strtolower(Auth::user()->pegawai->jabatan) == 'hotel manager')
                            {{-- Button Export Excel --}}
                            <div class="card-body px-4">
                                <button class="btn btn-success w-100" id="btnExport"><i class="bx bxs-file-export"></i>
                                    Export
                                    Data</button>
                                <div class="row" id="exportFilter">
                                    <div class="col-md-3">
                                        <label for="filterDateStart" class="form-label mt-3">Tanggal Awal:</label>
                                        <input type="date" id="filterDateStart" name="filterDateStart"
                                            class="form-control" />
                                    </div>
                                    <div class="col-md-3">
                                        <label for="filterDateEnd" class="form-label mt-3">Tanggal Akhir:</label>
                                        <input type="date" id="filterDateEnd" name="filterDateEnd"
                                            class="form-control" />
                                    </div>
                                    <div class="col-md-3">
                                        <label for="filterDateStart" class="form-label mt-3">Departemen:</label>
                                        <select id="filterDepartemen" name="filterDepartemen" class="form-select">
                                            <option value="">Semua Departemen</option>
                                            <option value="A&G">A&G</option>
                                            <option value="ACCOUNTING">ACCOUNTING</option>
                                            <option value="ENGINEERING">ENGINEERING</option>
                                            <option value="FB PRODUCT">FB PRODUCT</option>
                                            <option value="FB SERVICE">FB SERVICE</option>
                                            <option value="FRONT OFFICE">FRONT OFFICE</option>
                                            <option value="HOUSEKEEPING">HOUSEKEEPING</option>
                                            <option value="HRD">HRD</option>
                                            <option value="SALES & MARKETING">SALES & MARKETING</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="btnDownload" class="form-label mt-3">&nbsp;</label>
                                        <button class="btn btn-primary w-100" id="btnDownload"><i
                                                class="bx bxs-download"></i>
                                            <span id="lblDownload">Download</span></button>
                                    </div>
                                </div>
                            </div>
                            {{-- END Button Export Excel --}}
                        @endif
                        {{-- Table history absensi --}}
                        <table id="usersTable" class="table table-responsive table-striped text-nowrap px-4 table-sm"
                            style="width:100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nama Pegawai</th>
                                    <th>Departemen</th>
                                    <th>Shift</th>
                                    <th>Shift Masuk</th>
                                    <th>Shift Pulang</th>
                                    <th>Check In</th>
                                    <th>Check Out</th>
                                    <th>Status</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!--/ Attendance -->
        <div class="col-12 col-md-8 col-lg-12 col-xxl-4 order-1 order-md-2">
            <div class="row">
                <div class="col-12 mb-6">
                    <div class="card">

                        <div class="card-body d-flex flex-column align-items-center text-center">
                            <!-- Bagian atas: Nama + Shift -->
                            <div class="mb-4">
                                <h4 class="mb-1" id="nama-pegawai">
                                    {{ strtoupper(Auth::user()->pegawai->nama_pegawai) }}
                                </h4>
                                <span class="badge bg-label-info fs-5" id="shift-pegawai">
                                    Shift {{ isset($shiftAktif) ? ucfirst($shiftAktif) . ' ' . $waktuShiftAktif : '' }}
                                </span>
                            </div>

                            <!-- Bagian bawah: Tombol di tengah -->
                            <div class="d-flex justify-content-center w-100">
                                <button class="btn btn-primary rounded-circle fs-2" style="width:175px;height:175px;"
                                    data-bs-toggle="modal" data-bs-target="#backDropModal">
                                    {{ $statusAbsen }}
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
                {{-- <div class="col-6 mb-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <p class="badge bg-label-warning fs-5 mb-1">Kehadiran</p>
                            <h4 class="card-title fs-1 text-center">2</h4>
                        </div>
                    </div>
                </div>
                <div class="col-6 mb-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <p class="badge bg-label-primary fs-5 mb-1">Cuti</p>
                            <h4 class="card-title fs-1 text-center">12</h4>
                        </div>
                    </div>
                </div> --}}
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="backDropModal" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog modal-fullscreen">
            <form class="modal-content" id="formAbsen" method="post" action="{{ route('absen.store') }}"
                enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="backDropModalTitle" class="text-light">Absensi Wajah</h5>
                    {{-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> --}}
                </div>
                <div class="modal-body p-0">
                    <div class="row m-0 h-100">
                        <div class="col-12 p-0 mb-0 h-100">
                            <!-- Elemen video untuk menampilkan feed kamera -->
                            <video id="videoKamera" autoplay playsinline muted width="100%" height="100%"
                                class="d-block w-100 h-100"
                                style="border: 1px solid #ddd; border-radius: 5px; object-fit: cover;">
                            </video>
                            {{-- <p class="text-muted small mt-2 d-none d-md-block">
                                Arahkan kamera ke wajah Anda untuk absensi.
                            </p> --}}
                        </div>
                    </div>
                </div>
                <!-- Panel Informasi (Putih di bawah video) -->
                <div class="position-absolute bottom-0 w-100 bg-white text-center py-3 shadow-lg" style="opacity: 0.95;">
                    <input type="hidden" name="Baselatitude" id="Baselatitude">
                    <input type="hidden" name="Baselongitude" id="Baselongitude">
                    <input type="hidden" name="Baseradius" id="Baseradius">
                    <input type="hidden" name="latitude" id="latitude">
                    <input type="hidden" name="longitude" id="longitude">
                    <span id="lblRadius"></span>
                    <h6 id="namaPegawai" class="mb-1 fw-bold text-dark">{{ strtoupper(Auth::user()->pegawai->nama_pegawai) }}</h6>
                    <p id="tanggalSekarang" class="mb-0 text-muted" style="font-size: 0.9rem;"></p>
                    <p id="jamSekarang" class="mb-2 text-primary fw-semibold" style="font-size: 1.1rem;"></p>
                    <button type="button" class="btn btn-outline-secondary btn-sm"
                        data-bs-dismiss="modal">Close</button>
                    &nbsp;
                    <button type="button" id="btnSaveAbsensi" class="btn btn-primary btn-sm">Simpan</button>
                </div>
                {{-- <div class="modal-footer pt-0 pb-2 px-2">
                </div> --}}
            </form>
        </div>
    </div>

    {{-- Modal Foto Absen --}}
    <div class="modal fade" id="modalFoto" tabindex="-1" aria-labelledby="modalFotoLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalFotoLabel">Foto Pegawai</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <h5 id="namaPegawaiModal"></h5>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <p><b>Check In</b></p>
                            <img id="pictIn" src="" alt="Pict In" class="img-fluid rounded shadow">
                        </div>
                        <div class="col-md-6">
                            <p><b>Check Out</b></p>
                            <img id="pictOut" src="" alt="Pict Out" class="img-fluid rounded shadow">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- END Modal Foto Absen --}}

@endsection

@push('scripts')
    <script>
        window.addEventListener('load', function() {
            // Script Modal Foto
            $(document).on('click', '.btn-show-foto', function(e) {
                e.preventDefault();

                const nama = $(this).data('nama');
                const pictIn = $(this).data('pict-in');
                const pictOut = $(this).data('pict-out');

                $('#namaPegawaiModal').text(nama);
                $('#pictIn').attr('src', pictIn || 'https://via.placeholder.com/300x300?text=No+Image');
                $('#pictOut').attr('src', pictOut || 'https://via.placeholder.com/300x300?text=No+Image');

                $('#modalFoto').modal('show');
            });
            // END Script Modal Foto

            const btnSaveAbsensi = document.getElementById('btnSaveAbsensi');
            const formAbsen = document.getElementById('formAbsen');
            btnSaveAbsensi.disabled = true;

            function updateTanggalDanJam() {
                const now = new Date();

                // Format tanggal Indonesia
                const optionsTanggal = {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                };
                const tanggal = now.toLocaleDateString('id-ID', optionsTanggal);
                const jam = now.toLocaleTimeString('id-ID', {
                    hour12: false
                });

                document.getElementById('tanggalSekarang').textContent = tanggal;
                document.getElementById('jamSekarang').textContent = jam;
            }

            // Jalankan setiap detik
            setInterval(updateTanggalDanJam, 1000);
            updateTanggalDanJam();


            // Export Function
            const btnDownload = document.getElementById('btnDownload');

            if (btnDownload) {
                btnDownload.addEventListener('click', function() {
                    var lblDownload = document.getElementById('lblDownload');
                    lblDownload.innerHTML = "Processing...";
                    var startDate = document.getElementById('filterDateStart').value;
                    var endDate = document.getElementById('filterDateEnd').value;

                    if (startDate === '' || endDate === '') {
                        alert('Tanggal Awal dan Tanggal Akhir harus di isi');
                        lblDownload.innerHTML = "Download"; // Kembali ke Download jika validasi gagal
                        return;
                    }
                    const formData = new FormData();
                    formData.append('filterDateStart', document.getElementById('filterDateStart').value);
                    formData.append('filterDateEnd', document.getElementById('filterDateEnd').value);
                    formData.append('filterDepartemen', document.getElementById('filterDepartemen').value);
                    formData.append('_token', '{{ csrf_token() }}'); // CSRF token untuk Laravel
                    // Kirim via fetch (AJAX)
                    fetch('{{ route('attendance.export') }}', {
                            method: 'POST',
                            body: formData,
                        })
                        .then(response => {
                            if (response.ok) {
                                return response.blob(); // Mendapatkan file sebagai blob
                            } else {
                                lblDownload.innerHTML = 'Download';
                                throw new Error('Export gagal');
                            }
                        })
                        .then(blob => {
                            // Buat link download otomatis
                            const url = window.URL.createObjectURL(blob);
                            const a = document.createElement('a');
                            a.href = url;
                            a.download = 'attendance_' + new Date().toISOString().slice(0, 19).replace(
                                /:/g,
                                '-') + '.xlsx';
                            document.body.appendChild(a);
                            a.click();
                            a.remove();
                            // Set kembali ke Download jika error
                            lblDownload.innerHTML = "Download";
                            window.URL.revokeObjectURL(url);
                        })
                        .catch(error => {
                            // Set kembali ke Download jika error
                            lblDownload.innerHTML = "Download";
                            alert('Terjadi kesalahan: ' + error.message);
                        });

                });
            }
            // End Export Function

            // Event saat modal dibuka: Scroll ke atas + akses kamera
            document.getElementById('backDropModal').addEventListener('shown.bs.modal', async function() {
                // Scroll halaman utama ke atas secara otomatis
                window.scrollTo(0, 0);

                // Akses kamera (kamera depan untuk selfie/absensi)
                navigator.mediaDevices.getUserMedia({
                        video: {
                            facingMode: 'user', // Kamera depan
                            width: {
                                ideal: 1280
                            },
                            height: {
                                ideal: 720
                            }
                        }
                    })
                    .then(function(stream) {
                        var video = document.getElementById('videoKamera');
                        video.srcObject = stream;
                        video.play(); // Pastikan play di mobile
                    })
                    .catch(function(err) {
                        console.error('Error accessing camera: ', err);
                        alert(
                            'Gagal mengakses kamera. Izinkan permission di browser dan coba lagi.'
                        );
                        // Optional: Tutup modal jika gagal
                        // bootstrap.Modal.getInstance(document.getElementById('backDropModal')).hide();
                    });

                // Cek Lokasi
                // --- Ambil parameter lokasi dari server
                const lokasiData = await fetchConfigLokasi();
                if (!lokasiData) {
                    alert('Gagal memuat konfigurasi lokasi.');
                    return;
                }

                // --- Ambil posisi sekarang
                if (!navigator.geolocation) {
                    alert('Browser tidak mendukung geolocation.');
                    return;
                }

                // Set caption awal saat mulai mengambil lokasi
                document.getElementById('lblRadius').innerHTML = '⏳ sedang mengambil lokasi ....';
                $('#lblRadius').removeClass('text-success text-danger').addClass(
                    'text-info'); // Opsional: tambahkan class untuk indikasi loading
                btnSaveAbsensi.disabled = true; // Pastikan tombol disabled saat loading

                navigator.geolocation.getCurrentPosition(function(posisi) {
                    const currentLat = posisi.coords.latitude;
                    const currentLng = posisi.coords.longitude;

                    document.getElementById('latitude').value = currentLat;
                    document.getElementById('longitude').value = currentLng;

                    const paramLat = lokasiData.latitude;
                    const paramLng = lokasiData.longitude;
                    const radius = lokasiData.radius;

                    const dalamRadius = isInsideRadius(paramLat, paramLng, radius, currentLat,
                        currentLng);

                    if (dalamRadius) {
                        btnSaveAbsensi.disabled = false;
                        document.getElementById('lblRadius').innerHTML =
                            '✅' + @json($statusAbsen) + ' Ready !';
                        $('#lblRadius').addClass('text-success');
                        console.log('✅ Anda berada dalam radius lokasi yang diizinkan');
                    } else {
                        btnSaveAbsensi.disabled = true;
                        document.getElementById('lblRadius').innerHTML =
                            '❌ Anda Diluar Radius Absen !';
                        $('#lblRadius').addClass('text-danger');
                        console.log('❌ Anda berada di luar radius lokasi');
                    }
                }, function(error) {
                    console.error('Gagal mendapatkan lokasi:', error);
                });
                // END Cek Lokasi
            });

            async function fetchConfigLokasi() {
                try {
                    const res = await fetch(`/config/lokasi/lokasi`);
                    const data = await res.json();
                    document.getElementById('Baselatitude').value = data.latitude;
                    document.getElementById('Baselongitude').value = data.longitude;
                    document.getElementById('Baseradius').value = data.radius;
                    return data;
                } catch (error) {
                    console.error('Error fetch lokasi:', error);
                    return null;
                }
            }

            //  Menghitung radius lokasi sekarang dengan Parameter Lokasi
            function isInsideRadius(paramLat, paramLng, radiusMeter, currentLat, currentLng) {
                if ([paramLat, paramLng, radiusMeter, currentLat, currentLng].some(isNaN)) {
                    console.warn('❗ Data koordinat tidak valid');
                    return false;
                }

                const R = 6371e3;
                const lat1 = paramLat * Math.PI / 180;
                const lat2 = currentLat * Math.PI / 180;
                const deltaLat = (currentLat - paramLat) * Math.PI / 180;
                const deltaLng = (currentLng - paramLng) * Math.PI / 180;

                const a = Math.sin(deltaLat / 2) ** 2 +
                    Math.cos(lat1) * Math.cos(lat2) *
                    Math.sin(deltaLng / 2) ** 2;

                const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
                const distance = R * c;

                console.log(`Jarak dari titik pusat: ${distance.toFixed(2)} meter`);
                return distance <= radiusMeter;
            }
            // END Menghitung radius lokasi sekarang dengan Parameter Lokasi


            // KAMERA
            // Definisikan elemen-elemen yang diperlukan (INI YANG KURANG DI SCRIPT ANDA)
            const video = document.getElementById('videoKamera');
            const modal = document.getElementById('backDropModal');
            let mediaStream = null; // Untuk menyimpan stream kamera

            // Cek apakah elemen ada (debugging)
            if (!modal) {
                console.error('Elemen modal #backDropModal tidak ditemukan. Pastikan HTML modal ada di halaman.');
                return;
            }
            if (!video) {
                console.error('Elemen video #videoKamera tidak ditemukan. Pastikan HTML video ada di modal.');
                return;
            }
            console.log('Elemen modal dan video ditemukan. Siap inisialisasi kamera.'); // Debugging

            // Fungsi untuk memulai kamera (INI YANG KURANG - HARUS DIPANGGIL SAAT MODAL DIBUKA)
            function startCamera() {
                if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                    alert('Browser Anda tidak mendukung akses kamera. Gunakan Chrome/Firefox terbaru.');
                    return;
                }

                console.log('Memulai akses kamera...'); // Debugging

                navigator.mediaDevices.getUserMedia({
                        video: {
                            width: {
                                ideal: 640
                            },
                            height: {
                                ideal: 480
                            },
                            facingMode: 'user' // Kamera depan untuk absensi wajah (selfie)
                        }
                    })
                    .then(function(stream) {
                        mediaStream = stream;
                        video.srcObject = stream;
                        video.play(); // Pastikan video mulai play
                        console.log(
                            'Kamera berhasil diakses! Feed harus muncul sekarang.'); // Debugging: Cek console
                    })
                    .catch(function(err) {
                        console.error('Error mengakses kamera:', err);
                        let errorMsg = 'Gagal mengakses kamera. Alasan: ';
                        if (err.name === 'NotAllowedError') {
                            errorMsg +=
                                'Izin kamera ditolak. Izinkan di pengaturan browser (klik ikon gembok/kamera di address bar).';
                        } else if (err.name === 'NotFoundError') {
                            errorMsg += 'Tidak ada kamera ditemukan di perangkat Anda.';
                        } else if (err.name === 'NotReadableError') {
                            errorMsg += 'Kamera sedang digunakan aplikasi lain. Tutup aplikasi tersebut.';
                        } else if (err.name === 'OverconstrainedError') {
                            errorMsg += 'Spesifikasi kamera tidak sesuai (coba tanpa facingMode).';
                        } else {
                            errorMsg += err.message + '. Pastikan halaman di HTTPS/localhost.';
                        }
                        alert(errorMsg);
                        // Opsional: Tutup modal jika gagal
                        const modalInstance = bootstrap.Modal.getInstance(modal);
                        if (modalInstance) modalInstance.hide();
                    });
            }

            // Fungsi untuk menghentikan kamera (untuk hemat baterai dan privasi)
            function stopCamera() {
                if (mediaStream) {
                    mediaStream.getTracks().forEach(track => track.stop());
                    mediaStream = null;
                    video.srcObject = null;
                    console.log('Kamera dihentikan.'); // Debugging
                }
            }

            // Event listener: Mulai kamera saat modal dibuka (INI YANG HARUS DIUNCOMMENT DAN DITAMBAHKAN)
            modal.addEventListener('shown.bs.modal', function() {
                console.log('Modal dibuka - mulai kamera...'); // Debugging
                startCamera();
            });

            // Event listener: Hentikan kamera saat modal ditutup (UNCOMMENT DAN AKTIFKAN)
            modal.addEventListener('hidden.bs.modal', function() {
                console.log('Modal ditutup - hentikan kamera...'); // Debugging
                stopCamera();
            });

            // Event untuk tombol Save (capture gambar) - SEKARANG VARIABEL VIDEO DAN MODAL SUDAH ADA
            // Elemen loading buffer (bisa kamu styling sesuai Bootstrap)
            const loadingOverlay = document.createElement('div');
            loadingOverlay.innerHTML = `
  <div id="loadingOverlay" style="
      position: fixed;
      top: 0; left: 0; right: 0; bottom: 0;
      background: rgba(0,0,0,0.5);
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 1.2rem;
      z-index: 2000;
      display: none;
  ">
      <div class="spinner-border text-light me-2"></div> Mengirim absensi...
  </div>
`;
            document.body.appendChild(loadingOverlay);
            const overlay = document.getElementById('loadingOverlay');

            if (btnSaveAbsensi) {
                btnSaveAbsensi.addEventListener('click', async function() {
                    btnSaveAbsensi.disabled = true;
                    btnSaveAbsensi.innerHTML = '⏳ Menyimpan...';

                    try {
                        // 1️⃣ Ambil gambar dari video
                        const canvas = document.createElement('canvas');
                        canvas.width = video.videoWidth;
                        canvas.height = video.videoHeight;
                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

                        // // 🔸 Pause kamera sementara (preview mode)
                        // video.pause(); // Hentikan tampilan video agar terlihat “pause”
                        // const stream = video.srcObject;
                        // if (stream) {
                        //     const tracks = stream.getTracks();
                        //     tracks.forEach(track => track.enabled =
                        //         false); // Nonaktifkan kamera sementara
                        // }

                        // 2️⃣ Konversi ke blob (file)
                        const blob = await new Promise(resolve => canvas.toBlob(resolve, 'image/png'));
                        if (!blob) {
                            alert('❌ Gagal menangkap gambar dari kamera.');
                            return;
                        }
                        const file = new File([blob], `absensi-${Date.now()}.png`, {
                            type: 'image/png'
                        });

                        // 3️⃣ Ambil koordinat lokasi
                        const latitude = document.getElementById('latitude').value;
                        const longitude = document.getElementById('longitude').value;
                        const Baseradius = document.getElementById('Baseradius').value;
                        const Baselatitude = document.getElementById('Baselatitude').value;
                        const Baselongitude = document.getElementById('Baselongitude').value;
                        const shiftAktif = @json(isset($shiftAktif) ? strtolower($shiftAktif) : '');
                        const waktuShiftAktif = @json(isset($waktuShiftAktif) ? strtolower($waktuShiftAktif) : '');

                        // 4️⃣ Siapkan data form
                        const formData = new FormData();
                        formData.append('_token', '{{ csrf_token() }}');
                        formData.append('latitude', latitude);
                        formData.append('longitude', longitude);
                        formData.append('Baseradius', Baseradius);
                        formData.append('Baselatitude', Baselatitude);
                        formData.append('Baselongitude', Baselongitude);
                        formData.append('shiftAktif', shiftAktif);
                        formData.append('waktuShiftAktif', waktuShiftAktif);
                        formData.append('foto_absensi', file);

                        console.log('File : ', file);
                        console.log('Form Data ', formData);
                        for (const [key, value] of formData.entries()) {
                            console.log(key, value, value instanceof File ? '(File detected ✅)' : '');
                        }

                        // 5️⃣ Kirim ke Laravel pakai fetch
                        const response = await fetch('{{ route('absen.store') }}', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]').content
                            }
                        });
                        // 🔍 Debugging: Log status dan isi respons
                        console.log('Response status:', response.status);
                        const responseText = await response.text(); // Ambil sebagai teks dulu
                        console.log('Response text:', responseText);
                        // Jika respons bukan JSON, tampilkan error
                        if (!response.ok) {
                            alert('⚠️ Server error: ' +
                                responseText); // Tampilkan teks respons sebagai error
                            return;
                        }

                        // Jika OK, parse sebagai JSON
                        const result = JSON.parse(
                            responseText); // Atau gunakan response.json() jika yakin JSON
                        console.log('✅ Respon server:', result);
                        if (response.ok) {
                            alert('✅ Absensi berhasil disimpan!');
                            window.location.href = '/dashboard';
                        } else {
                            alert('⚠️ Terjadi kesalahan: ' + (result.message || 'Server error.'));
                        }

                    } catch (error) {
                        console.error('❌ Error saat menyimpan absensi:', error);
                        alert('Terjadi error saat menyimpan absensi.');
                    } finally {
                        btnSaveAbsensi.disabled = false;
                        btnSaveAbsensi.innerHTML = 'Simpan';
                    }
                });
            } else {
                console.error('❌ Tombol #btnSaveAbsensi tidak ditemukan di modal.');
            }


            console.log('Script kamera selesai diinisialisasi.'); // Debugging akhir
            // END KAMERA


            $('#usersTable').on('init.dt', function() {
                $('#usersTable_wrapper').addClass('px-5');
                $('.dt-layout-table').addClass('table-responsive');
            });

            let lastIsMobile = window.innerWidth < 578;

            $(window).on('resize', function() {
                let isMobile = window.innerWidth < 578;

                if (isMobile !== lastIsMobile) {
                    location.reload();
                }

                lastIsMobile = isMobile;
            });

            // Deteksi apakah mobile (resolusi < 578px)
            var isMobile = window.innerWidth < 578;

            if (isMobile) {
                // Hancurkan tabel lama
                $('#usersTable').DataTable().destroy();

                $('#usersTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: window.location.origin + '/datatable/history-absen',
                        type: 'GET'
                    },
                    columns: [{
                            data: 'DT_RowIndex', // Kunci dari addIndexColumn()
                            name: 'DT_RowIndex',
                            title: 'No.', // Ubah header ID menjadi NO.
                            orderable: false, // Nomor urut tidak perlu diurutkan
                            searchable: false, // Nomor urut tidak perlu dicari
                            className: 'no-column' // Tambahkan class untuk styling
                        },
                        {
                            data: 'nama_pegawai',
                            name: 'nama_pegawai',
                            className: 'nama-column' // Tambahkan class untuk styling
                        },
                        {
                            data: null, // Kolom baru untuk "More"
                            name: 'more',
                            title: 'More',
                            orderable: false,
                            searchable: false,
                            className: 'more-column', // Tambahkan class untuk styling
                            render: function(data, type, row) {
                                return '<button class="btn btn-sm btn-primary more-btn">More</button>';
                            }
                        },
                        {
                            data: 'departemen',
                            name: 'departemen',
                            className: 'hidden-mobile' // Class untuk hide di mobile
                        },
                        {
                            data: 'shift',
                            name: 'shift',
                            className: 'hidden-mobile'
                        },
                        {
                            data: 'shift_masuk',
                            name: 'shift_masuk',
                            className: 'hidden-mobile'
                        },
                        {
                            data: 'shift_pulang',
                            name: 'shift_pulang',
                            className: 'hidden-mobile'
                        },
                        {
                            data: 'check_in',
                            name: 'check_in',
                            className: 'hidden-mobile'
                        },
                        {
                            data: 'check_out',
                            name: 'check_out',
                            className: 'hidden-mobile'
                        },
                        {
                            data: 'status',
                            name: 'status',
                            className: 'hidden-mobile'
                        },
                        {
                            data: 'keterangan',
                            name: 'keterangan',
                            className: 'hidden-mobile'
                        }
                        // {
                        //     data: 'action',
                        //     name: 'action',
                        //     orderable: false,
                        //     searchable: false
                        // }
                    ],
                    language: {
                        // Mengganti string "Show _MENU_ entries" menjadi hanya "_MENU_" (dropdown)
                        lengthMenu: "_MENU_",
                        searchPlaceholder: "Cari...",
                        search: ""
                    },
                    responsive: false, // Nonaktifkan responsive otomatis DataTables agar kita kontrol penuh
                    pageLength: 10,
                    lengthMenu: [
                        [10, 25, 50, 100],
                        [10, 25, 50, 100]
                    ],
                    // 🔥 Tambahkan createdRow untuk pewarnaan baris berdasarkan is_late
                    createdRow: function(row, data, dataIndex) {
                        if (data.is_late || data.is_fast || data.keterangan == 'Tidak Check Out' || data
                            .keterangan == 'telat' || data.keterangan == 'cepat pulang') {
                            $(row).addClass('table-danger'); // Bootstrap class untuk warna kuning
                        }
                    }
                });
            } else {
                // Hancurkan tabel lama
                $('#usersTable').DataTable().destroy();

                $('#usersTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: window.location.origin + '/datatable/history-absen',
                        type: 'GET'
                    },
                    columns: [{
                            data: 'DT_RowIndex', // Kunci dari addIndexColumn()
                            name: 'DT_RowIndex',
                            title: 'No.', // Ubah header ID menjadi NO.
                            orderable: false, // Nomor urut tidak perlu diurutkan
                            searchable: false, // Nomor urut tidak perlu dicari
                            className: 'no-column' // Tambahkan class untuk styling
                        },
                        {
                            data: 'nama_pegawai',
                            name: 'nama_pegawai',
                            className: 'nama-column' // Tambahkan class untuk styling
                        },
                        {
                            data: 'departemen',
                            name: 'departemen',
                            className: 'hidden-mobile' // Class untuk hide di mobile
                        },
                        {
                            data: 'shift',
                            name: 'shift',
                            className: 'hidden-mobile'
                        },
                        {
                            data: 'shift_masuk',
                            name: 'shift_masuk',
                            className: 'hidden-mobile'
                        },
                        {
                            data: 'shift_pulang',
                            name: 'shift_pulang',
                            className: 'hidden-mobile'
                        },
                        {
                            data: 'check_in',
                            name: 'check_in',
                            className: 'hidden-mobile'
                        },
                        {
                            data: 'check_out',
                            name: 'check_out',
                            className: 'hidden-mobile'
                        },
                        {
                            data: 'status',
                            name: 'status',
                            className: 'hidden-mobile'
                        },
                        {
                            data: 'keterangan',
                            name: 'keterangan',
                            className: 'hidden-mobile'
                        }
                        // {
                        //     data: 'action',
                        //     name: 'action',
                        //     orderable: false,
                        //     searchable: false
                        // }
                    ],
                    language: {
                        // Mengganti string "Show _MENU_ entries" menjadi hanya "_MENU_" (dropdown)
                        lengthMenu: "_MENU_",
                        searchPlaceholder: "Cari...",
                        search: ""
                    },
                    responsive: false, // Nonaktifkan responsive otomatis DataTables agar kita kontrol penuh
                    pageLength: 10,
                    lengthMenu: [
                        [10, 25, 50, 100],
                        [10, 25, 50, 100]
                    ],
                    // 🔥 Tambahkan createdRow untuk pewarnaan baris berdasarkan is_late
                    createdRow: function(row, data, dataIndex) {
                        if (data.is_late || data.is_fast || data.keterangan == 'Tidak Check Out' || data
                            .keterangan == 'telat' || data.keterangan == 'cepat pulang') {
                            $(row).addClass('table-danger'); // Bootstrap class untuk warna kuning
                        }
                    }
                });
            }

            // Fungsi untuk format child row (data tambahan yang muncul saat "More" diklik)
            // Perbaikan: Kembalikan HTML yang sesuai untuk child row DataTables (akan dibungkus dalam <td colspan="...">)
            function format(d) {
                return '<div class="child-row" style="padding-left: 20px;">' +
                    // Hapus display: none, biarkan slide handle
                    '<table cellpadding="5" cellspacing="0" border="0">' +
                    '<tr>' +
                    '<td><strong>Departemen:</strong></td>' +
                    '<td>' + d.departemen + '</td>' +
                    '</tr>' +
                    '<tr>' +
                    '<td><strong>Shift:</strong></td>' +
                    '<td>' + d.shift + '</td>' +
                    '</tr>' +
                    '<tr>' +
                    '<td><strong>Shift Masuk:</strong></td>' +
                    '<td>' + d.shift_masuk + '</td>' +
                    '</tr>' +
                    '<tr>' +
                    '<td><strong>Shift Pulang:</strong></td>' +
                    '<td>' + d.shift_pulang + '</td>' +
                    '</tr>' +
                    '<tr>' +
                    '<td><strong>Check In:</strong></td>' +
                    '<td>' + d.check_in + '</td>' +
                    '</tr>' +
                    '<tr>' +
                    '<td><strong>Check Out:</strong></td>' +
                    '<td>' + d.check_out + '</td>' +
                    '</tr>' +
                    '<tr>' +
                    '<td><strong>Status:</strong></td>' +
                    '<td>' + d.status + '</td>' +
                    '</tr>' +
                    '<tr>' +
                    '<td><strong>Keterangan:</strong></td>' +
                    '<td>' + d.keterangan + '</td>' +
                    '</tr>' +
                    '</table>' +
                    '</div>';
            }

            // Event listener untuk tombol "More" (setelah DataTable diinisialisasi)
            $('#usersTable').on('click', '.more-btn', function() {
                var tr = $(this).closest('tr');
                var row = $('#usersTable').DataTable().row(tr);
                var childRow = $(row.node()).next(
                    'tr.child'); // Dapatkan child row yang benar (tr dengan class 'child')

                if (row.child.isShown()) {
                    // Jika child row sudah ditampilkan, sembunyikan dengan slide up
                    row.child.hide();
                    tr.removeClass('shown');
                } else {
                    // Jika belum, tampilkan dengan slide down
                    row.child(format(row.data())).show();
                    tr.addClass('shown');
                    // Tambahkan animasi slide down manual pada child row
                    childRow.find('.child-row').hide().slideDown(400); // 400ms animasi
                }
            });


            // Toggle filter export
            // Sembunyikan filter di awal (opsional)
            $('#exportFilter').hide();

            // Saat tombol Export diklik
            $('#btnExport').on('click', function(e) {
                e.preventDefault(); // mencegah reload jika tombol di dalam form
                $('#exportFilter').slideToggle(300); // toggle dengan animasi
            });

        });
    </script>

    <style>
        #usersTable tbody td {
            text-transform: capitalize;
        }

        /* Styling untuk child row */
        .child-row {
            background-color: #f9f9f9;
            border-left: 3px solid #007bff;
            margin-top: 10px;
        }

        /* Fullscreen modal di mobile - Hilangkan scrollbar dan scroll ke atas */
        @media (max-width: 575.98px) {
            .hidden-mobile {
                display: none !important;
            }

            .no-column,
            .nama-column,
            .more-column {
                display: table-cell !important;
                /* Pastikan selalu tampil */
            }

            .modal-fullscreen .modal-content {
                height: 100vh;
                border-radius: 0;
                overflow: hidden !important;
                /* Hilangkan overflow di seluruh modal */
            }

            .modal-fullscreen .modal-header {
                padding: 0.5rem 1rem;
                background: rgb(255, 255, 255);
                color: rgb(255, 255, 255);
            }

            .modal-fullscreen .modal-body {
                padding: 0 !important;
                height: calc(100vh - 120px);
                overflow: hidden !important;
                /* Pastikan tidak ada scroll di body */
            }

            .modal-fullscreen .modal-footer {
                padding: 0.5rem 1rem !important;
                background: rgba(0, 0, 0, 0.8);
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                z-index: 1050;
                border-top: 1px solid #dee2e6;
            }

            /* Video fullscreen di mobile */
            #videoKamera {
                width: 100vw !important;
                height: 100vh !important;
                object-fit: cover;
                border: none !important;
                border-radius: 0 !important;
                position: absolute;
                top: 0;
                left: 0;
                overflow: hidden;
                /* Hilangkan overflow di video */
            }

            .modal-fullscreen .btn-close {
                filter: invert(1);
            }

            .modal-fullscreen .btn {
                font-size: 0.875rem;
            }

            /* Hilangkan scrollbar di modal (untuk semua browser) */
            .modal-fullscreen::-webkit-scrollbar,
            .modal-fullscreen .modal-content::-webkit-scrollbar {
                display: none;
                /* Chrome/Safari */
            }

            .modal-fullscreen {
                -ms-overflow-style: none;
                /* IE/Edge */
                scrollbar-width: none;
                /* Firefox */
            }
        }

        /* Untuk desktop/laptop */
        @media (min-width: 576px) {
            #videoKamera {
                height: auto;
                max-height: 70vh;
            }
        }

        /* Global: Hilangkan scroll di body saat modal terbuka (perkuat Bootstrap) */
        body.modal-open {
            overflow: hidden !important;
            /* Pastikan body tidak scroll */
            position: fixed;
            /* Fixed body untuk mencegah shift layout di mobile */
            width: 100%;
            touch-action: none;
            /* Cegah touch scroll di iOS/Android */
        }

        /* Hilangkan scrollbar di body saat modal open */
        body.modal-open::-webkit-scrollbar {
            display: none;
        }

        body.modal-open {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
@endpush
