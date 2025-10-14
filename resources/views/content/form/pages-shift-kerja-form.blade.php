@extends('layouts/contentNavbarLayout')

@section('title', 'Shift Kerja - Forms')

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
                <h5 class="card-header"><i class="bx bx-lg bx-timer"></i> Form Shift Kerja / {{ $flag }} Data</h5>
                <div class="card-body col-12 mx-auto">
                    <div class="mb-4">
                        <label for="nama-pegawai" class="form-label fs-5">Nama Pegawai</label>
                        <input class="form-control {{ isset($data) ? 'bg-secondary bg-opacity-25' : '' }}"
                            list="datalistOptions" type="text"
                            value="{{ old('nama_pegawai', isset($data) ? $data->pegawai->nama_pegawai : '') }}"
                            placeholder="Type to search..." name="nama_pegawai" id="nama-pegawai"
                            {{ $flag !== 'Tambah' ? 'readonly' : '' }} autocomplete="off" />
                        <datalist id="datalistOptions"></datalist>
                    </div>
                    <div class="mb-4">
                        <label for="no-pegawai" class="form-label fs-5">Nomor Pegawai</label>
                        <input type="text" class="form-control bg-secondary bg-opacity-25" id="no-pegawai"
                            name="no_pegawai" autocomplete="off"
                            value="{{ old('no_pegawai', isset($data) ? $data->pegawai->no_pegawai : '') }}" readonly />
                    </div>
                    <div class="mb-4">
                        <label for="departemen" class="form-label fs-5">Departemen</label>
                        <input type="text" class="form-control bg-secondary bg-opacity-25" id="departemen"
                            name="departemen" autocomplete="off"
                            value="{{ old('departemen', isset($data) ? $data->pegawai->departemen : '') }}" readonly />
                    </div>
                    <div class="mb-4">
                        <label for="jenis_kelamin" class="form-label fs-5">Shift Kerja</label>
                        <select id="jenis_kelamin" class="form-select" name="jenis_kelamin">
                            <option hidden>-- Shift Kerja --</option>
                            <option {{ isset($data) && $data->shift == 'malam' ? 'selected' : '' }} value="malam">Malam
                            </option>
                            <option {{ isset($data) && $data->shift == 'pagi' ? 'selected' : '' }} value="pagi">Pagi
                            </option>
                            <option {{ isset($data) && $data->shift == 'siang' ? 'selected' : '' }} value="siang">Siang
                            </option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="waktu-masuk" class="form-label fs-5">Waktu Masuk</label>
                        <input type="time" class="form-control" id="waktu-masuk" name="waktu_masuk" autocomplete="off"
                            value="{{ isset($data) ? $data->waktu_masuk : '' }}" />
                    </div>
                    <div class="mb-4">
                        <label for="waktu-pulang" class="form-label fs-5">Waktu Pulang</label>
                        <input type="time" class="form-control" id="waktu-pulang" name="waktu_pulang"
                            value="{{ isset($data) ? $data->waktu_pulang : '' }}" />
                    </div>

                    <button class="btn btn-primary btn-lg w-100"><i class="bx bx-save"></i> Simpan</button>
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
                            // jika response berupa string atau object, ambil nilai nama dan no pegawai
                            const namaValue = (typeof item === 'string') ? item : (item.nama_pegawai ||
                                item.name || '');
                            const noValue = (typeof item === 'object') ? (item.no_pegawai || item.no ||
                                '') : '';
                            const departemenValue = (typeof item === 'object') ? (item.departemen ||
                                item.no ||
                                '') : '';
                            if (!namaValue) return;

                            const option = document.createElement('option');
                            // tampilkan nama dalam UPPERCASE (atau gunakan nama asli sesuai kebutuhan)
                            option.value = String(namaValue).toUpperCase();
                            // simpan no_pegawai di data attribute supaya bisa diambil saat dipilih
                            // if (noValue) option.dataset.noPegawai = noValue;
                            datalist.appendChild(option);

                            const hiddenNo = document.getElementById('no-pegawai');
                            const departemen = document.getElementById('departemen');
                            if (hiddenNo && noValue) {
                                hiddenNo.value = noValue; // langsung isi no_pegawai saat ada hasil
                            }
                            if (departemen && departemenValue) {
                                departemen.value =
                                departemenValue; // langsung isi no_pegawai saat ada hasil
                            }
                        });

                        // ketika user mengetik/memilih item dari datalist, isi input no-pegawai
                        // input.addEventListener('input', function() {
                        //     const val = this.value;
                        //     const match = Array.from(datalist.options).find(o => o.value === val);
                        //     const hiddenNo = document.getElementById('no-pegawai');
                        //     if (match && hiddenNo) {
                        //         hiddenNo.value = match.dataset.noPegawai || '';
                        //     } else if (hiddenNo) {
                        //         hiddenNo.value = ''; // bersihkan jika tidak ada match
                        //     }
                        // });
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
