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
                        data: 'waktu_masuk',
                        name: 'waktu_masuk'
                    },
                    {
                        data: 'waktu_pulang',
                        name: 'waktu_pulang'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
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
    </style>
@endpush
