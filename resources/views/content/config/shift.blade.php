@extends('layouts/contentNavbarLayout')

@section('title', 'Manajemen - Shift Kerja')

@section('page-style')
    {{-- Custom CSS untuk peta, marker Boxicons, dan circle radius --}}
@endsection

@section('content')
    <div class="row g-0">
        <div class="col-md-12">
            <div class="card">
                <h5 class="card-header"><i class="bx bx-lg bx-timer"></i> Manajemen / Shift Kerja
                </h5>
                <div class="card-body col-12 mx-auto">
                    {{-- Table history absensi --}}
                    <table id="manajemenShiftKerja" class="table table-responsive table-striped text-nowrap px-4 table-sm"
                        style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Shift Kerja</th>
                                <th>Waktu Masuk</th>
                                <th>Waktu Pulang</th>
                                <th>Updated</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        window.addEventListener('load', function() {
            $('#manajemenShiftKerja').on('init.dt', function() {
                $('#manajemenShiftKerja_wrapper').addClass('px-5');
                $('.dt-layout-table').addClass('table-responsive');
            });

            $('#manajemenShiftKerja').DataTable({
                processing: false,
                serverSide: false,
                searching: false,
                lengthChange: false,
                ajax: {
                    url: window.location.origin + '/datatable/param-shift-kerja',
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
                        data: 'val',
                        name: 'val'
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
                        data: 'updated_at',
                        name: 'updated_at'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                responsive: true,
                lengthMenu: [
                    [10, 25, 50, 100],
                    [10, 25, 50, 100]
                ]
            });
        });
    </script>
@endpush
