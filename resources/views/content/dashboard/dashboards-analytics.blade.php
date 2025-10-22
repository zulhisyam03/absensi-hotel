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
                        @if (strtolower(Auth::user()->pegawai->jabatan) == 'hr')
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
                                            Download</button>
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
                                    <th>Waktu Shift</th>
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
                                    Shift {{ isset($shiftAktif) ? ucfirst($shiftAktif) : '' }}
                                    <input type="hidden" name="shiftAktif"
                                        value="{{ isset($shiftAktif) ? strtolower($shiftAktif) : '' }}">
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
            <form class="modal-content">
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
                            <p class="text-muted small mt-2 d-none d-md-block">
                                Arahkan kamera ke wajah Anda untuk absensi.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer pt-0 pb-2 px-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                    &nbsp;
                    <button type="button" id="btnSaveAbsensi" class="btn btn-primary btn-sm">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        window.addEventListener('load', function() {

            // Export Function
            const btnDownload = document.getElementById('btnDownload');

            if (btnDownload) {
                btnDownload.addEventListener('click', function() {
                    var startDate = document.getElementById('filterDateStart').value;
                    var endDate = document.getElementById('filterDateEnd').value;

                    if (startDate === '' || endDate === '') {
                        alert('Tanggal Awal dan Tanggal Akhir harus di isi');
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
                            window.URL.revokeObjectURL(url);
                        })
                        .catch(error => {
                            alert('Terjadi kesalahan: ' + error.message);
                        });
                });
            }
            // End Export Function

            // Event saat modal dibuka: Scroll ke atas + akses kamera
            document.getElementById('backDropModal').addEventListener('shown.bs.modal', function() {
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
                        alert('Gagal mengakses kamera. Izinkan permission di browser dan coba lagi.');
                        // Optional: Tutup modal jika gagal
                        // bootstrap.Modal.getInstance(document.getElementById('backDropModal')).hide();
                    });
            });

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
            const btnSaveAbsensi = document.getElementById('btnSaveAbsensi');
            if (btnSaveAbsensi) {
                btnSaveAbsensi.addEventListener('click', function() {
                    if (!video || !video.videoWidth || !video.videoHeight) {
                        alert(
                            'Kamera belum siap. Pastikan feed kamera muncul dulu (lihat console untuk error).'
                        );
                        return;
                    }

                    console.log('Mencoba capture gambar...'); // Debugging

                    // Ambil snapshot dari video (menggunakan canvas)
                    const canvas = document.createElement('canvas');
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(video, 0, 0);

                    // Konversi ke base64 atau kirim ke server untuk proses absensi
                    const imageData = canvas.toDataURL('image/png');
                    console.log('Gambar absensi berhasil dicapture:', imageData.substring(0, 50) +
                        '... (lihat console lengkap)'); // Debugging: Tampilkan sebagian data

                    // Tutup modal setelah save
                    const modalInstance = bootstrap.Modal.getInstance(modal);
                    if (modalInstance) {
                        modalInstance.hide();
                    }

                    // Opsional: Tampilkan pesan sukses
                    alert('Absensi berhasil disimpan! (Gambar ada di console untuk testing)');

                    // Opsional: Download gambar untuk testing (hapus jika tidak perlu)
                    const link = document.createElement('a');
                    link.href = imageData;
                    link.download = 'absensi-' + new Date().toISOString().slice(0, 19).replace(/:/g, '-') +
                        '.png';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                });
            } else {
                console.error('Tombol #btnSaveAbsensi tidak ditemukan di modal.');
            }

            console.log('Script kamera selesai diinisialisasi.'); // Debugging akhir
            // END KAMERA


            $('#usersTable').on('init.dt', function() {
                $('#usersTable_wrapper').addClass('px-5');
                $('.dt-layout-table').addClass('table-responsive');
            });

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
                        searchable: false // Nomor urut tidak perlu dicari
                    },
                    {
                        data: 'nama_pegawai',
                        name: 'nama_pegawai'
                    },
                    {
                        data: 'departemen',
                        name: 'departemen'
                    },
                    {
                        data: 'shift',
                        name: 'shift'
                    },
                    {
                        data: 'waktu_shift',
                        name: 'waktu_shift'
                    },
                    {
                        data: 'check_in',
                        name: 'check_in'
                    },
                    {
                        data: 'check_out',
                        name: 'check_out'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'keterangan',
                        name: 'keterangan'
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
                responsive: true,
                pageLength: 10,
                lengthMenu: [
                    [10, 25, 50, 100],
                    [10, 25, 50, 100]
                ],
                // 🔥 Tambahkan createdRow untuk pewarnaan baris berdasarkan is_late
                createdRow: function(row, data, dataIndex) {
                    if (data.is_late || data.keterangan == 'Tidak Check Out') {
                        $(row).addClass('table-danger'); // Bootstrap class untuk warna kuning
                    }
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

        /* Fullscreen modal di mobile - Hilangkan scrollbar dan scroll ke atas */
        @media (max-width: 575.98px) {
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
                max-height: 60vh;
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
