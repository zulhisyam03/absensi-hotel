@extends('layouts/contentNavbarLayout')

@section('title', 'Pegawai')

@section('vendor-style')
    @vite('resources/assets/vendor/libs/apex-charts/apex-charts.scss')
@endsection

@section('vendor-script')
    @vite('resources/assets/vendor/libs/apex-charts/apexcharts.js')
@endsection

@section('page-script')
    @vite('resources/assets/js/dashboards-analytics.js')
@endsection

@section('page-style')
    <style>
        /* Custom styles for the page can be added here */
        .modal-body span {
            text-transform: uppercase;
            font-style: italic;
            font-weight: bold;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <!-- Attendence -->
        <div class="col-12 col-xxl-12 order-2 order-md-3 order-xxl-2 mb-6">
            <div class="card">
                <div class="row row-bordered g-0">
                    <div class="col-lg-12">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <div class="card-title mb-0">
                                <h5 class="m-0 me-2"><i class="bx bx-lg bxs-user-account"></i> Daftar Pegawai</h5>
                            </div>
                        </div>

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

                        @if (strtolower(Auth::user()->pegawai->jabatan) == 'hr')
                            {{-- Button Export Excel --}}
                            <div class="card-body px-4">
                                <button class="btn btn-success w-100" id="btnExport"><i class="bx bxs-file-export"></i>
                                    Export
                                    Data</button>
                                <div class="row justify-content-center" id="exportFilter">
                                    <div class="col-md-4">
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
                                    <div class="col-md-4">
                                        <label for="btnDownload" class="form-label mt-3">&nbsp;</label>
                                        <button class="btn btn-primary w-100" id="btnDownload"><i
                                                class="bx bxs-download"></i>
                                            Download</button>
                                    </div>
                                </div>
                            </div>
                            {{-- END Button Export Excel --}}

                            {{-- Button tambah Data Pegwai --}}
                            <div class="d-flex justify-content-end mb-5">
                                <button class="btn btn-dark btn-lg mx-5 float-end w-100" id="btnTambahPegawai"
                                    onclick="navigateToForm()"><i class="bx bx-lg bx-plus"></i>
                                    Tambah Pegawai</button>
                            </div>
                            {{-- END Button tambah Data Pegwai --}}
                        @endif

                        {{-- Table history absensi --}}
                        <table id="pegawaiTable" class="table table-responsive table-striped text-nowrap px-4 table-sm"
                            style="width:100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Pegawai</th>
                                    <th>Nomor Pegawai</th>
                                    <th>Departemen</th>
                                    <th>No. Handphone</th>
                                    <th>Tanggal Join</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLongTitle">Detail Pegawai</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 pe-2">
                            <div class="mb-4">
                                <img src="" id="foto-pegawai" alt="foto-pegawai"
                                    style="width: 200px; height: 200px; object-fit: cover;" class="d-block mx-auto rounded">
                            </div>
                            <div class="mb-4">
                                <label for="nama-pegawai" class="form-label fs-6 col-md-4">NAMA PEGAWAI</label>
                                <span class="col-md-1">:</span>
                                <span id="nama-pegawai"></span>
                            </div>
                            <div class="mb-4">
                                <label for="nik" class="form-label fs-6 col-md-4">NIK</label>
                                <span class="col-md-1">:</span>
                                <span id ='nik'></span>
                            </div>
                            <div class="mb-4">
                                <label for="npwp" class="form-label fs-6 col-md-4">NPWP</label>
                                <span class="col-md-1">:</span>
                                <span id="npwp"></span>
                            </div>
                            <div class="mb-4">
                                <label for="bpjs" class="form-label fs-6 col-md-4">BPJS</label>
                                <span class="col-md-1">:</span>
                                <span id="bpjs"></span>
                            </div>
                            <div class="mb-4">
                                <label for="no-pegawai" class="form-label fs-6 col-md-4">NOMOR PEGAWAI</label>
                                <span class="col-md-1">:</span>
                                <span id="no-pegawai"></span>
                            </div>
                            <div class="mb-4">
                                <label for="email" class="form-label fs-6 col-md-4">EMAIL</label>
                                <span class="col-md-1">:</span>
                                <span id="email"></span>
                            </div>
                            <div class="mb-4">
                                <label for="no-handphone" class="form-label fs-6 col-md-4">NO. HANDPHONE</label>
                                <span class="col-md-1">:</span>
                                <span id="no-handphone"></span>
                            </div>
                            <div class="mb-4">
                                <label for="emergency-number" class="form-label fs-6 col-md-4">EMERGENCY NUMBER</label>
                                <span class="col-md-1">:</span>
                                <span id="emergency-number"></span>
                            </div>
                            <div class="mb-4">
                                <label for="tempat-lahir" class="form-label fs-6 col-md-4">TEMPAT LAHIR</label>
                                <span class="col-md-1">:</span>
                                <span id="tempat-lahir"></span>
                            </div>
                            <div class="mb-4">
                                <label for="tanggal-lahir" class="form-label fs-6 col-md-4">TANGGAL LAHIR</label>
                                <span class="col-md-1">:</span>
                                <span id="tanggal-lahir"></span>
                            </div>
                            <div class="mb-4">
                                <label for="jenis-kelamin" class="form-label fs-6 col-md-4">JENIS KELAMIN</label>
                                <span class="col-md-1">:</span>
                                <span id="jenis-kelamin"></span>
                            </div>
                            <div class="mb-4">
                                <label for="alamat" class="form-label fs-6 col-md-4">ALAMAT</label>
                                <span class="col-md-1">:</span>
                                <span id="alamat"></span>
                            </div>
                            <div class="mb-4">
                                <label for="tanggal-join" class="form-label fs-6 col-md-4">TANGGAL JOIN</label>
                                <span class="col-md-1">:</span>
                                <span id="tanggal-join"></span>
                            </div>
                            <div class="mb-4">
                                <label for="jabatan" class="form-label fs-6 col-md-4">JABATAN</label>
                                <span class="col-md-1">:</span>
                                <span id="jabatan"></span>
                            </div>
                            <div class="mb-4">
                                <label for="departemen" class="form-label fs-6 col-md-4">DEPARTEMEN</label>
                                <span class="col-md-1">:</span>
                                <span id="departemen"></span>
                            </div>
                            <div class="mb-4">
                                <label for="status-pegawai" class="form-label fs-6 col-md-4">STATUS PEGAWAI</label>
                                <span class="col-md-1">:</span>
                                <span id="status-pegawai"></span>
                            </div>
                            <div class="mb-4">
                                <label for="last-salary" class="form-label fs-6 col-md-4">LAST SALARY</label>
                                <span class="col-md-1">:</span>
                                <span id="last-salary"></span>
                            </div>
                        </div>
                        {{-- END RIGHT COLUMN --}}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
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
                    const formData = new FormData();
                    formData.append('filterDepartemen', document.getElementById('filterDepartemen').value);
                    formData.append('_token', '{{ csrf_token() }}'); // CSRF token untuk Laravel
                    // Kirim via fetch (AJAX)
                    fetch('{{ route('pegawai.export') }}', {
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

            $('#pegawaiTable').on('init.dt', function() {
                $('#pegawaiTable_wrapper').addClass('px-5');
                $('.dt-layout-table').addClass('table-responsive');
            });

            $('#pegawaiTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: window.location.origin + '/datatable/pegawai',
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
                        data: 'no_pegawai',
                        name: 'no_pegawai'
                    },
                    {
                        data: 'departemen',
                        name: 'departemen'
                    },
                    {
                        data: 'no_hp',
                        name: 'no_hp'
                    },
                    {
                        data: 'tgl_join',
                        name: 'tgl_join'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                // dom: 'Bfrtip', // tombol di atas table
                buttons: [{
                        extend: 'excelHtml5',
                        text: 'Excel',
                        className: 'btn btn-success btn-sm',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4]
                        }
                    },
                    {
                        extend: 'csvHtml5',
                        text: 'CSV',
                        className: 'btn btn-info btn-sm',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4]
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        text: 'PDF',
                        className: 'btn btn-danger btn-sm',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4]
                        }
                    },
                    {
                        extend: 'print',
                        text: 'Print',
                        className: 'btn btn-secondary btn-sm',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4]
                        }
                    }
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
                ]
            });

            // Toggle filter export
            // Sembunyikan filter di awal (opsional)
            $('#exportFilter').hide();

            // Saat tombol Export diklik
            $('#btnExport').on('click', function(e) {
                e.preventDefault(); // mencegah reload jika tombol di dalam form
                $('#exportFilter').slideToggle(300); // toggle dengan animasi
            });

            document.addEventListener('click', function(e) {
                const btn = e.target.closest('.delete-btn');
                const btnDetail = e.target.closest('.btnDetail');

                // Delete Button
                if (btn) {
                    e.preventDefault();
                    const id = btn.dataset.id;
                    if (!id) return;

                    if (!confirm('Yakin ingin menghapus Pegawai ini? Tindakan ini tidak dapat dibatalkan.'))
                        return;

                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute(
                            'content') ||
                        '{{ csrf_token() }}';
                    const url = '{{ url('/pages/pegawai/delete') }}' + '/' + encodeURIComponent(id);

                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = url;
                    form.style.display = 'none';

                    const inputToken = document.createElement('input');
                    inputToken.type = 'hidden';
                    inputToken.name = '_token';
                    inputToken.value = token;
                    form.appendChild(inputToken);

                    const inputMethod = document.createElement('input');
                    inputMethod.type = 'hidden';
                    inputMethod.name = '_method';
                    inputMethod.value = 'DELETE';
                    form.appendChild(inputMethod);

                    document.body.appendChild(form);
                    form.submit();
                }

                // === Detail Pegawai ===
                if (btnDetail) {
                    e.preventDefault();

                    const id = btnDetail.dataset.id;

                    // Jalankan fungsi Ambil data pegawai
                    showPegawaiDetail(id);
                }
            });

            function showPegawaiDetail(pegawaiId) {
                // Lakukan permintaan AJAX untuk mendapatkan detail pegawai
                $.ajax({
                    url: window.location.origin + '/pegawai/' + pegawaiId + '/detail',
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        // Isi data ke dalam modal
                        $('#foto-pegawai').attr('src', data.foto);
                        $('#nama-pegawai').text(data.nama_pegawai);
                        $('#nik').text(data.nik);
                        $('#npwp').text(data.npwp);
                        $('#bpjs').text(data.bpjs);
                        $('#no-pegawai').text(data.no_pegawai);
                        $('#email').text(data.email);
                        $('#no-handphone').text(data.no_hp);
                        $('#emergency-number').text(data.emergency_number);
                        $('#tempat-lahir').text(data.tempat_lahir);
                        $('#tanggal-lahir').text(data.tgl_lahir);
                        $('#jenis-kelamin').text(data.jenis_kelamin);
                        $('#alamat').text(data.alamat);
                        $('#tanggal-join').text(data.tgl_join);
                        $('#jabatan').text(data.jabatan);
                        $('#departemen').text(data.departemen);
                        $('#status-pegawai').text(data.status_pegawai);
                        $('#last-salary').text((formatRupiah(data.last_salary)));

                        // Tampilkan modal
                        // detailModal.show();
                    },
                    error: function() {
                        alert('Gagal mengambil data pegawai.');
                    }
                });
            }
        });

        // Fungsi helper untuk format ke Rupiah
        function formatRupiah(angka) {
            if (!angka) return '-';
            return 'Rp. ' + parseInt(angka)
                .toString()
                .replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        function navigateToForm() {
            window.location.href = window.location.origin + '/pages/pegawai/create';
        }
    </script>

    <style>
        #pegawaiTable tbody td {
            text-transform: capitalize;
        }
    </style>
@endpush
