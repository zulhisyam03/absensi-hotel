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
                                <h5 class="m-0 me-2"><i class="bx bx-lg bx-timer"></i> Shift Kerja</h5>
                            </div>
                        </div>
                        {{-- Table history absensi --}}
                        <table id="usersTable" class="table table-responsive table-striped text-nowrap px-4"
                            style="width:100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nama Pegawai</th>
                                    <th>Shift</th>
                                    <th>Waktu Masuk</th>
                                    <th>Waktu Pulang</th>
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
            $('#usersTable').on('init.dt', function() {
                $('#usersTable_wrapper').addClass('px-5');
                $('.dt-layout-table').addClass('table-responsive');
            });

            $('#usersTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('history-absen.index') }}',
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
        #usersTable tbody td {
            text-transform: capitalize;
        }
    </style>
@endpush
