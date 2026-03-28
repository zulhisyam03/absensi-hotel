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

                    {{-- Button tambah Data Config Shift --}}
                    <div class="d-flex justify-content-end mb-5">
                        <button class="btn btn-dark btn-lg mx-5 float-end w-100" id="btnTambahConfigShift"
                            onclick="location.href='{{ route('config-shift-kerja.create') }}'"><i
                                class="bx bx-lg bx-plus"></i>
                            Tambah Parameter Shift Kerja</button>
                    </div>
                    {{-- END Button tambah Data Config Shift --}}

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

        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.delete-btn');
            if (!btn) return;
            e.preventDefault();

            const id = btn.dataset.id;
            if (!id) return;

            if (!confirm('Yakin ingin menghapus parameter ini? Tindakan ini tidak dapat dibatalkan.')) return;

            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                '{{ csrf_token() }}';
            const url = '{{ url('/config/shift-kerja/delete') }}' + '/' + encodeURIComponent(id);

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
@endpush
