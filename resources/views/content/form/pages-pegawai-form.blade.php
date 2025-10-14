@extends('layouts/contentNavbarLayout')

@section('title', 'Pegawai - Forms')

@section('page-script')
    {{-- @vite('resources/assets/js/form-basic-inputs.js') --}}
@endsection

@section('page-style')
    <style>
        input[type="text"] {
            text-transform: capitalize;
        }

        #no-pegawai {
            text-transform: uppercase;
        }
    </style>
@section('content')
    <div class="row g-12">
        <div class="col-md-12">
            <div class="card">
                <h5 class="card-header"><i class="bx bx-lg bxs-user-account"></i> Form Pegawai / {{ $flag }} Data
                </h5>
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                        </button>
                    </div>
                @endif
                <form id="formPegawai" method="post"
                    action="{{ isset($data) ? route('pegawai.update', $data->id) : route('pegawai.store') }}">
                    @csrf
                    {{-- Handle Update Form --}}
                    @if (isset($data))
                        @method('PUT')
                    @endif
                    {{-- END Handle Update Form --}}
                    <div class="card-body">
                        <div class="row g-2">
                            {{-- LEFT COLUMN --}}
                            <div class="col-md-6 pe-2">
                                <div class="mb-4">
                                    <label for="nama-pegawai" class="form-label fs-5">Nama Pegawai</label>
                                    <input type="text" class="form-control" id="nama-pegawai" placeholder="Nama Pegawai"
                                        value="{{ old('nama_pegawai', isset($data) ? $data->nama_pegawai : '') }}"
                                        name="nama_pegawai" autocomplete="off" />
                                    @error('nama_pegawai')
                                        <div class="text-danger mt-2">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="mb-4">
                                    <label for="nik" class="form-label fs-5">NIK</label>
                                    <input type="text" class="form-control" id="nik" placeholder="NIK"
                                        value="{{ old('nik', isset($data) ? $data->nik : '') }}" name="nik"
                                        autocomplete="off" />
                                    @error('nik')
                                        <div class="text-danger mt-2">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="mb-4">
                                    <label for="npwp" class="form-label fs-5">NPWP</label>
                                    <input type="text" class="form-control" id="npwp" placeholder="NPWP"
                                        value="{{ old('npwp', isset($data) ? $data->npwp : '') }}" name="npwp"
                                        autocomplete="off" />
                                    @error('npwp')
                                        <div class="text-danger mt-2">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="mb-4">
                                    <label for="bpjs" class="form-label fs-5">BPJS</label>
                                    <input type="text" class="form-control" id="bpjs" placeholder="BPJS"
                                        value="{{ old('bpjs', isset($data) ? $data->bpjs : '') }}" name="bpjs"
                                        autocomplete="off" />
                                    @error('bpjs')
                                        <div class="text-danger mt-2">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="mb-4">
                                    <label for="no-pegawai" class="form-label fs-5">Nomor Pegawai</label>
                                    <input type="text" class="form-control" id="no-pegawai" placeholder="Nomor Pegawai"
                                        value="{{ old('no_pegawai', isset($data) ? $data->no_pegawai : '') }}"
                                        name="no_pegawai" autocomplete="off" />
                                    @error('no_pegawai')
                                        <div class="text-danger mt-2">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="mb-4">
                                    <label for="email" class="form-label fs-5">Email</label>
                                    <input type="email" class="form-control" id="email"
                                        placeholder="example@gmail.com"
                                        value="{{ old('email', isset($data) ? $data->email : '') }}" name="email"
                                        autocomplete="off" />
                                    @error('email')
                                        <div class="text-danger mt-2">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="mb-4">
                                    <label for="no-handphone" class="form-label fs-5">No. Handphone</label>
                                    <input type="text" class="form-control" id="no-handphone" placeholder="0812xxxxxxxx"
                                        value="{{ old('no_handphone', isset($data) ? $data->no_hp : '') }}"
                                        name="no_handphone" autocomplete="off" />
                                    @error('no_handphone')
                                        <div class="text-danger mt-2">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="mb-4">
                                    <label for="emergency-number" class="form-label fs-5">Emergency Numbe</label>
                                    <input type="text" class="form-control" id="emergency-number"
                                        placeholder="0812xxxxxxxx"
                                        value="{{ old('emergency_number', isset($data) ? $data->emergency_number : '') }}"
                                        name="emergency_number" autocomplete="off" />
                                    @error('emergency_number')
                                        <div class="text-danger mt-2">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="mb-4">
                                    <label for="tempat-lahir" class="form-label fs-5">Tempat Lahir</label>
                                    <input type="text" class="form-control" id="tempat-lahir"
                                        placeholder="Tempat Lahir"
                                        value="{{ old('tempat_lahir', isset($data) ? $data->tempat_lahir : '') }}"
                                        name="tempat_lahir" autocomplete="off" />
                                    @error('tempat_lahir')
                                        <div class="text-danger mt-2">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            {{-- END LEF COLUMN --}}

                            {{-- RIGHT COLUMN --}}
                            <div class="col-md-6 ps-2">
                                <div class="mb-4">
                                    <label for="tanggal-lahir" class="form-label fs-5">Tanggal Lahir</label>
                                    <div class="col-md-12">
                                        <input class="form-control" type="date" name="tanggal_lahir"
                                            id="tanggal-lahir"
                                            value="{{ old('tanggal_lahir', isset($data) ? $data->tgl_lahir : '') }}" />
                                    </div>
                                    @error('tanggal_lahir')
                                        <div class="text-danger mt-2">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="mb-4">
                                    <label for="jenis-kelamin" class="form-label fs-5">Jenis Kelamin</label>
                                    <select id="jenis-kelamin" class="form-select" name="jenis_kelamin">
                                        <option hidden value="">-- Jenis Kelamin --</option>
                                        <option @selected(old('jenis_kelamin', $data->jenis_kelamin ?? '') == 'L') value="L">
                                            Laki-Laki</option>
                                        <option @selected(old('jenis_kelamin', $data->jenis_kelamin ?? '') == 'P') value="P">
                                            Perempuan</option>
                                    </select>
                                    @error('jenis_kelamin')
                                        <div class="text-danger mt-2">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="mb-4">
                                    <label for="alamat" class="form-label fs-5">Alamat</label>
                                    <input type="text" class="form-control" id="alamat" placeholder="Alamat"
                                        name="alamat" value="{{ old('alamat', isset($data) ? $data->alamat : '') }}"
                                        autocomplete="off" />
                                    @error('alamat')
                                        <div class="text-danger mt-2">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="mb-4">
                                    <label for="tanggal-join" class="form-label fs-5">Tanggal Join</label>
                                    <input type="date" class="form-control" id="tanggal-join" name="tanggal_join"
                                        value="{{ old('tanggal_join', isset($data) ? $data->tgl_join : '') }}" />
                                    @error('tanggal_join')
                                        <div class="text-danger mt-2">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="mb-4">
                                    <label for="jabatan" class="form-label fs-5">Jabatan</label>
                                    <select id="jabatan" class="form-select" name="jabatan">
                                        <option hidden value="">-- Jabatan --</option>
                                        <option @selected(old('jabatan', $data->jabatan ?? '') == 'HR') value="HR">HR
                                        </option>
                                        <option @selected(old('jabatan', $data->jabatan ?? '') == 'Staff') value="Staff">
                                            Staff
                                        </option>
                                    </select>
                                    @error('jabatan')
                                        <div class="text-danger mt-2">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="mb-4">
                                    <label for="departemen" class="form-label fs-5">Departemen</label>
                                    <select id="departemen" class="form-select" name="departemen">
                                        <option hidden value="">-- Departemen --</option>
                                        <option @selected(old('departemen', $data->departemen ?? '') == 'A&G') value="A&G">
                                            A&G</option>
                                        <option @selected(old('departemen', $data->departemen ?? '') == 'ACCOUNTING') value="ACCOUNTING">
                                            ACCOUNTING</option>
                                        <option @selected(old('departemen', $data->departemen ?? '') == 'FB PRODUCT') value="FB PRODUCT">
                                            FB PRODUCT</option>
                                        <option @selected(old('departemen', $data->departemen ?? '') == 'FB SERVICE') value="FB SERVICE">
                                            FB SERVICE</option>
                                        <option @selected(old('departemen', $data->departemen ?? '') == 'FRONT OFFICE') value="FRONT OFFICE">
                                            FRONT OFFICE</option>
                                        <option @selected(old('departemen', $data->departemen ?? '') == 'HOUSEKEEPING') value="HOUSEKEEPING">
                                            HOUSEKEEPING</option>
                                        <option @selected(old('departemen', $data->departemen ?? '') == 'HRD') value="HRD">
                                            HRD</option>
                                        <option @selected(old('departemen', $data->departemen ?? '') == 'SALES & MARKETING') value="SALES & MARKETING">
                                            SALES & MARKETING</option>
                                    </select>
                                    @error('departemen')
                                        <div class="text-danger mt-2">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="mb-4">
                                    <label for="status-pegawai" class="form-label fs-5">Status Pegawai</label>
                                    <select id="status-pegawai" class="form-select" name="status_pegawai">
                                        <option hidden value="">-- Departemen --</option>
                                        <option @selected(old('status_pegawai', $data->status_pegawai ?? '') == 'Casual') value="Casual">
                                            Casual</option>
                                        <option @selected(old('status_pegawai', $data->status_pegawai ?? '') == 'DW') value="DW">
                                            DW</option>
                                        <option @selected(old('status_pegawai', $data->status_pegawai ?? '') == 'Kontrak') value="Kontrak">
                                            Kontrak</option>
                                        <option @selected(old('status_pegawai', $data->status_pegawai ?? '') == 'Training') value="Training">
                                            Training</option>
                                    </select>
                                    @error('status_pegawai')
                                        <div class="text-danger mt-2">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="mb-4">
                                    <label for="last-salary" class="form-label fs-5">Last Salary</label>
                                    {{-- <input type="text" class="form-control" id="last-salary" placeholder="1.000.000"
                                        name="last_salary"
                                        value="{{ old('last_salary', isset($data) ? $data->last_salary : '') }}"
                                        autocomplete="off" />
                                    @error('last_salary')
                                        <div class="text-danger mt-2">
                                            {{ $message }}
                                        </div>
                                    @enderror --}}
                                    {{-- visible formatted input (no name) --}}
                                    <input type="text" class="form-control" id="last-salary-display"
                                        placeholder="1.000.000" autocomplete="off" />
                                    {{-- hidden input yang dikirim ke server (hanya angka) --}}
                                    <input type="hidden" id="last-salary-hidden" name="last_salary"
                                        value="{{ old('last_salary', isset($data) ? $data->last_salary : '') }}" />
                                    @error('last_salary')
                                        <div class="text-danger mt-2">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            {{-- END RIGHT COLUMN --}}
                        </div>

                        <button class="btn btn-primary btn-lg w-100 mt-4"><i class="bx bx-save"></i> Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const display = document.getElementById('last-salary-display');
            const hidden = document.getElementById('last-salary-hidden');
            if (!display || !hidden) return;

            // format angka -> 1.234.567
            function formatDisplay(value) {
                const digits = String(value || '').replace(/\D/g, '');
                if (!digits) return '';
                return digits.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }

            // inisialisasi dari hidden value (server)
            display.value = formatDisplay(hidden.value);

            // coba simpan caret di akhir (sederhana)
            function setCaretToEnd(el) {
                try {
                    el.setSelectionRange(el.value.length, el.value.length);
                } catch (e) {}
            }

            // input handler: hanya angka, tampilkan formatted, simpan raw di hidden
            display.addEventListener('input', function(e) {
                const raw = this.value.replace(/\D/g, '');
                this.value = formatDisplay(raw);
                hidden.value = raw;
                setCaretToEnd(this);
            });

            // batasi keypress ke angka dan kontrol
            display.addEventListener('keydown', function(e) {
                const allowed = ['Backspace', 'ArrowLeft', 'ArrowRight', 'Delete', 'Tab', 'Home', 'End'];
                if (allowed.includes(e.key)) return;
                if ((e.ctrlKey || e.metaKey) && ['a', 'c', 'v', 'x'].includes(e.key.toLowerCase())) return;
                if (e.key >= '0' && e.key <= '9') return;
                e.preventDefault();
            });

            // handle paste: ambil hanya digit
            display.addEventListener('paste', function(e) {
                e.preventDefault();
                const text = (e.clipboardData || window.clipboardData).getData('text') || '';
                const digits = text.replace(/\D/g, '');
                if (!digits) return;
                this.value = formatDisplay(digits);
                hidden.value = digits;
                setCaretToEnd(this);
            });

            // pastikan sebelum submit hidden terisi
            const form = display.closest('form');
            if (form) {
                form.addEventListener('submit', function() {
                    hidden.value = (display.value || '').replace(/\D/g, '');
                });
            }
        });
    </script>
