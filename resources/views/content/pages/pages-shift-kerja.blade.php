@extends('layouts/contentNavbarLayout')

@section('title', 'Shift Kerja')

@section('vendor-style')
    @vite('resources/assets/vendor/libs/apex-charts/apex-charts.scss')
@endsection

@section('vendor-script')
    @vite('resources/assets/vendor/libs/apex-charts/apexcharts.js')
@endsection

@section('page-script')
    @vite('resources/assets/js/dashboards-analytics.js')
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
                                <h5 class="m-0 me-2"><i class="bx bx-lg bx-timer"></i> Shift Kerja</h5>
                            </div>
                        </div>

                        @if (session('error'))
                            <div class="alert alert-danger d-flex alert-dismissible mx-5" role="alert">
                                <span class="alert-icon rounded-circle">
                                    <i class="icon-base bx bxs-error-circle icon-sm"></i>
                                </span>
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                </button>
                            </div>
                        @endif

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

                        @if (strtolower(Auth::user()->pegawai->jabatan) == 'hr' || strtolower(Auth::user()->pegawai->jabatan) == 'supervisor')
                            {{-- Button tambah Data Pegwai --}}
                            <div class="d-flex justify-content-end mb-5">
                                <button class="btn btn-dark btn-lg mx-5 float-end w-100" id="btnTambahShift"
                                    onclick="navigateToForm()"><i class="bx bx-lg bx-plus"></i>
                                    Tambah Shift</button>
                            </div>
                            {{-- END Button tambah Data Pegwai --}}
                        @endif

                        {{-- Table history absensi --}}
                        <table id="shiftTable" class="table table-responsive table-striped text-nowrap px-4 table-sm"
                            style="width:100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Pegawai</th>
                                    <th>Departemen</th>
                                    <th>Shift</th>
                                    <th>Waktu Masuk</th>
                                    <th>Waktu Pulang</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        window.addEventListener('load', function() {
            $('#shiftTable').on('init.dt', function() {
                $('#shiftTable_wrapper').addClass('px-5');
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
                $('#shiftTable').DataTable().destroy();

                $('#shiftTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: window.location.origin + '/datatable/shift-kerja',
                        type: 'GET'
                    },
                    columns: [{
                            data: 'DT_RowIndex', // Kunci dari addIndexColumn()
                            name: 'DT_RowIndex',
                            title: 'No.', // Ubah header ID menjadi NO.
                            orderable: false, // Nomor urut tidak perlu diurutkan
                            searchable: false, // Nomor urut tidak perlu dicari
                            className: 'nama-column' // Hide di mobile
                        },
                        {
                            data: 'nama_pegawai',
                            name: 'nama_pegawai',
                            className: 'nama-column' // Tambahkan class untuk styling
                        },
                        {
                            data: 'departemen',
                            name: 'departemen',
                            className: 'hidden-mobile' // Tambahkan class untuk styling
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
                            data: 'shift',
                            name: 'shift',
                            className: 'hidden-mobile' // Class untuk hide di mobile
                        },
                        {
                            data: 'waktu_masuk',
                            name: 'waktu_masuk',
                            className: 'hidden-mobile'
                        },
                        {
                            data: 'waktu_pulang',
                            name: 'waktu_pulang',
                            className: 'hidden-mobile'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false,
                            className: 'hidden-mobile' // Hide di mobile
                        }
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
                    ]
                });
            } else {
                // Hancurkan tabel lama
                $('#shiftTable').DataTable().destroy();

                $('#shiftTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: window.location.origin + '/datatable/shift-kerja',
                        type: 'GET'
                    },
                    columns: [{
                            data: 'DT_RowIndex', // Kunci dari addIndexColumn()
                            name: 'DT_RowIndex',
                            title: 'No.', // Ubah header ID menjadi NO.
                            orderable: false, // Nomor urut tidak perlu diurutkan
                            searchable: false, // Nomor urut tidak perlu dicari
                            className: 'hidden-mobile' // Hide di mobile
                        },
                        {
                            data: 'nama_pegawai',
                            name: 'nama_pegawai',
                            className: 'nama-column' // Tambahkan class untuk styling
                        },
                        {
                            data: 'departemen',
                            name: 'departemen',
                            className: 'departemen-column' // Tambahkan class untuk styling
                        },
                        {
                            data: 'shift',
                            name: 'shift',
                            className: 'hidden-mobile' // Class untuk hide di mobile
                        },
                        {
                            data: 'waktu_masuk',
                            name: 'waktu_masuk',
                            className: 'hidden-mobile'
                        },
                        {
                            data: 'waktu_pulang',
                            name: 'waktu_pulang',
                            className: 'hidden-mobile'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false,
                            className: 'hidden-mobile' // Hide di mobile
                        }
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
                    ]
                });
            }


            // Fungsi untuk format child row (data tambahan yang muncul saat "More" diklik)
            function formatShift(d) {
                return '<div class="child-row" style="padding-left: 20px;">' +
                    // Hapus display: none, biarkan slide handle
                    '<table cellpadding="5" cellspacing="0" border="0">' +
                    '<tr>' +
                    '<td><strong>Department.:</strong></td>' +
                    '<td>' + d.departemen + '</td>' +
                    '</tr>' +
                    '<tr>' +
                    '<td><strong>Shift:</strong></td>' +
                    '<td>' + d.shift + '</td>' +
                    '</tr>' +
                    '<tr>' +
                    '<td><strong>Waktu Masuk:</strong></td>' +
                    '<td>' + d.waktu_masuk + '</td>' +
                    '</tr>' +
                    '<tr>' +
                    '<td><strong>Waktu Pulang:</strong></td>' +
                    '<td>' + d.waktu_pulang + '</td>' +
                    '</tr>' +
                    '<tr>' +
                    '<td><strong>Action:</strong></td>' +
                    '<td>' + d.action + '</td>' +
                    '</tr>' +
                    '</table>' +
                    '</div>';
            }

            // Event listener untuk tombol "More" (setelah DataTable diinisialisasi)
            $('#shiftTable').on('click', '.more-btn', function() {
                var tr = $(this).closest('tr');
                var row = $('#shiftTable').DataTable().row(tr);
                var childRow = $(row.node()).next(
                    'tr.child'); // Dapatkan child row yang benar (tr dengan class 'child')

                if (row.child.isShown()) {
                    // Jika child row sudah ditampilkan, sembunyikan dengan slide up
                    row.child.hide();
                    tr.removeClass('shown');
                } else {
                    // Jika belum, tampilkan dengan slide down
                    row.child(formatShift(row.data())).show();
                    tr.addClass('shown');
                    // Tambahkan animasi slide down manual pada child row
                    childRow.find('.child-row').hide().slideDown(400); // 400ms animasi
                }
            });
        });

        function navigateToForm() {
            window.location.href = window.location.origin + '/pages/shift-kerja/create';
        }

        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.delete-btn');
            if (!btn) return;
            e.preventDefault();

            const id = btn.dataset.id;
            if (!id) return;

            if (!confirm('Yakin ingin menghapus Shift Pegawai ini? Tindakan ini tidak dapat dibatalkan.')) return;

            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                '{{ csrf_token() }}';
            const url = '{{ url('/pages/shift-kerja/delete') }}' + '/' + encodeURIComponent(id);

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
        });
    </script>

    <style>
        #shiftTable tbody td {
            text-transform: capitalize;
        }

        /* Styling untuk child row */
        .child-row {
            background-color: #f9f9f9;
            border-left: 3px solid #007bff;
            margin-top: 10px;
        }

        /* Opsional: Styling untuk kolom yang selalu tampil di mobile */
        @media (max-width: 578px) {
            .hidden-mobile {
                display: none !important;
            }

            .nama-column,
            .departemen-column,
            .more-column {
                display: table-cell !important;
                /* Pastikan selalu tampil */
            }
        }
    </style>
@endpush
