@extends('layouts/contentNavbarLayout')

@section('title', 'Dashboard - Analytics')

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
                                <h5 class="m-0 me-2"><i class="bx bx-lg bx-user"></i> Daftar Pegawai</h5>
                            </div>
                        </div>

                        {{-- Button tambah Data Pegwai --}}
                        <div class="d-flex justify-content-end">
                            <button class="btn btn-dark btn-lg mx-5 float-end w-100" id="btnTambahPegawai"><i
                                    class="bx bx-lg bx-plus"></i>
                                Tambah Pegawai</button>
                        </div>
                        {{-- END Button tambah Data Pegwai --}}

                        {{-- Table history absensi --}}
                        <table id="pegawaiTable" class="table table-responsive table-striped text-nowrap px-4"
                            style="width:100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nomor Pegawai</th>
                                    <th>Nama Pegawai</th>
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
@endsection

@push('scripts')
    <script>
        window.addEventListener('load', function() {
            $('#pegawaiTable').on('init.dt', function() {
                $('#pegawaiTable_wrapper').addClass('px-5');
                $('.dt-layout-table').addClass('table-responsive');
            });

            $('#pegawaiTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('pegawai.index') }}',
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
                        data: 'no_karyawan',
                        name: 'no_karyawan'
                    },
                    {
                        data: 'nama_pegawai',
                        name: 'nama_pegawai'
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
    </script>

    <style>
        #pegawaiTable tbody td {
            text-transform: capitalize;
        }
    </style>
@endpush
