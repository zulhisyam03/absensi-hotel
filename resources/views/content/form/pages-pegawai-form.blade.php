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
                    <div class="card-body col-12 mx-auto">
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
                            <label for="no-pegawai" class="form-label fs-5">Nomor Pegawai</label>
                            <input type="text" class="form-control" id="no-pegawai" placeholder="Nomor Pegawai"
                                value="{{ old('no_pegawai', isset($data) ? $data->no_pegawai : '') }}" name="no_pegawai"
                                autocomplete="off" />
                            @error('no_pegawai')
                                <div class="text-danger mt-2">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="email" class="form-label fs-5">Email</label>
                            <input type="email" class="form-control" id="email" placeholder="example@gmail.com"
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
                                value="{{ old('no_handphone', isset($data) ? $data->no_hp : '') }}" name="no_handphone"
                                autocomplete="off" />
                            @error('no_handphone')
                                <div class="text-danger mt-2">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="tempat-lahir" class="form-label fs-5">Tempat Lahir</label>
                            <input type="text" class="form-control" id="tempat-lahir" placeholder="Tempat Lahir"
                                value="{{ old('tempat_lahir', isset($data) ? $data->tempat_lahir : '') }}"
                                name="tempat_lahir" autocomplete="off" />
                            @error('tempat_lahir')
                                <div class="text-danger mt-2">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="tanggal-lahir" class="col-md-2 col-form-label">Tanggal Lahir</label>
                            <div class="col-md-12">
                                <input class="form-control" type="date" name="tanggal_lahir" id="tanggal-lahir"
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
                            <input type="text" class="form-control" id="alamat" placeholder="Alamat" name="alamat"
                                value="{{ old('alamat', isset($data) ? $data->alamat : '') }}" autocomplete="off" />
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

                        <button class="btn btn-primary btn-lg w-100"><i class="bx bx-save"></i> Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
