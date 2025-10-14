@extends('layouts/contentNavbarLayout')

@section('title', 'Payroll - Forms')

@section('page-script')
    {{-- @vite('resources/assets/js/form-basic-inputs.js') --}}
@endsection

@section('page-style'){
    <style>
        input[type="text"] {
            text-transform: uppercase;
        }
    </style>
    }
@endsection

@section('content')
    <div class="row g-12">
        <div class="col-md-12">
            <div class="card">
                <h5 class="card-header"><i class="bx bx-lg bx-dollar-circle"></i> Payroll</h5>
                <div class="card-body col-12 mx-auto">
                    <div class="mb-4">
                        <label for="defaultFormControlInput" class="form-label fs-5">Periode</label>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <select id="defaultSelect" class="form-select" name="periode_bulan">
                                    <option hidden>-- Bulan --</option>
                                    <option value="01">Januari</option>
                                    <option value="02">Februari</option>
                                    <option value="03">Maret</option>
                                    <option value="04">April</option>
                                    <option value="05">Mei</option>
                                    <option value="06">Juni</option>
                                    <option value="07">Juli</option>
                                    <option value="08">Agustus</option>
                                    <option value="09">September</option>
                                    <option value="10">Oktober</option>
                                    <option value="11">November</option>
                                    <option value="12">Desember</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <select id="defaultSelect" class="form-select" name="periode_tahun">
                                    <option hidden>-- Tahun --</option>
                                    <option value="2025">2025</option>
                                    <option value="2024">2024</option>
                                    <option value="2023">2023</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="nama-pegawai" class="form-label fs-5">Nama Pegawai</label>
                        <input class="form-control" list="datalistOptions" type="text" id="nama-pegawai"
                            placeholder="Type to search..." name="nama_pegawai" autocomplete="off" />
                        <datalist id="datalistOptions"></datalist> {{-- Datalist kosong, diisi JS --}}
                        @error('nama_pegawai')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        {{-- <div id="defaultFormControlHelp" class="form-text">We'll never share your details with anyone else.
                        </div> --}}
                    </div>

                    <div class="mb-4">
                        <label for="no-pegawai" class="form-label fs-5">Nomor Pegawai</label>
                        <input type="text" class="form-control bg-secondary bg-opacity-25" id="no-pegawai"
                            name="no_pegawai" autocomplete="off"
                            value="{{ old('no_pegawai', isset($data) ? $data->pegawai->no_pegawai : '') }}" readonly />
                    </div>

                    <div class="mb-4">
                        <label for="defaultFormControlInput" class="form-label fs-5">Total Hari Kerja</label>
                        <input type="number" class="form-control" id="exampleFormControlInput1"
                            placeholder="Total Hari Kerja" name="total_hari_kerja" readonly />
                    </div>
                    <div class="mb-4">
                        <label for="defaultFormControlInput" class="form-label fs-5">Total Gaji</label>
                        <input type="text" class="form-control" id="exampleFormControlInput1" placeholder="Rp. 1.000.000"
                            name="total_gaji" />
                    </div>

                    <button class="btn btn-warning btn-lg w-100"><i class="bx bx-printer"></i> Cetak</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const input = document.getElementById('nama-pegawai');
            const datalist = document.getElementById('datalistOptions');
            let timeout; // Untuk debounce

            if (!input || !datalist) return; // Safety check

            // Event listener saat user ketik
            input.addEventListener('input', function() {
                const query = this.value.trim();

                // Clear debounce timeout
                clearTimeout(timeout);

                // Jika query kosong atau terlalu pendek, clear datalist
                if (query.length === 0 || query.length < 2) {
                    datalist.innerHTML = '';
                    return;
                }

                // Debounce: Delay 300ms
                timeout = setTimeout(function() {
                    fetchPegawai(query);
                }, 300);
            });

            // Function fetch dari web route /pegawai/search
            function fetchPegawai(query) {
                // URL web route (tanpa /api)
                const url = `/pegawai/search?q=${encodeURIComponent(query)}`;

                // Tampilkan loading (opsional)
                datalist.innerHTML = '<option value="Searching...">Searching...</option>';

                fetch(url, {
                        method: 'GET',
                        credentials: 'same-origin', // Otomatis include session cookie untuk auth
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest' // Opsional: Tandai sebagai AJAX request
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            if (response.status === 401) {
                                throw new Error('Unauthorized - Please login');
                            }
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        // Clear datalist
                        datalist.innerHTML = '';

                        // Asumsi response adalah array langsung (adjust jika pakai wrapper {data: [...]})
                        const results = Array.isArray(data) ? data : (data.data || []);

                        if (results.length === 0) {
                            const option = document.createElement('option');
                            option.value = 'Tidak ada hasil ditemukan';
                            option.disabled = true;
                            datalist.appendChild(option);
                            return;
                        }

                        // Tambah option untuk setiap nama
                        results.forEach(item => {
                            // jika response berupa string atau object, ambil nilai nama yang benar
                            const namaValue = (typeof item === 'string') ? item : (item.nama_pegawai ||
                                item.name || '');
                            const noValue = (typeof item === 'string') ? '' : (item.no_pegawai || '');

                            const hiddenNo = document.getElementById('no-pegawai');
                            if (!namaValue) return;

                            console.log(item);
                            const option = document.createElement('option');
                            // set value uppercase
                            option.value = String(namaValue).toUpperCase();
                            datalist.appendChild(option);
                            // set hidden input no pegawai
                            if (hiddenNo && noValue) {
                                hiddenNo.value = noValue;
                            }
                        });
                    })
                    .catch(error => {
                        console.error('Error fetching pegawai:', error);
                        datalist.innerHTML =
                            '<option value="Error: Gagal memuat data">Error: Gagal memuat data</option>';
                        // Opsional: Redirect ke login jika 401
                        if (error.message.includes('Unauthorized')) {
                            window.location.href = '/login';
                        }
                    });
            }
        });
    </script>
@endpush
