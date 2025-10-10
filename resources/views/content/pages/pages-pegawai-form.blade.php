@extends('layouts/contentNavbarLayout')

@section('title', 'Pegawai - Forms')

@section('page-script')
    {{-- @vite('resources/assets/js/form-basic-inputs.js') --}}
@endsection

@section('content')
    <div class="row g-12">
        <div class="col-md-12">
            <div class="card">
                <h5 class="card-header"><i class="bx bx-lg bx-user"></i> Form Pegawai / Tambah Data</h5>
                <div class="card-body col-12 mx-auto">
                    <div class="mb-4">
                        <label for="nama_pegawai" class="form-label fs-5">Nama Pegawai</label>
                        <input type="text" class="form-control" id="nama_pegawai" placeholder="Nama Pegawai"
                            name="nama_pegawai" autocomplete="off" />
                    </div>
                    <div class="mb-4">
                        <label for="nomor_pegawai" class="form-label fs-5">Nomor Pegawai</label>
                        <input type="text" class="form-control" id="nomor_pegawai" placeholder="Nomor Pegawai"
                            name="nomor_pegawai" autocomplete="off" />
                    </div>
                    <div class="mb-4">
                        <label for="email" class="form-label fs-5">Email</label>
                        <input type="email" class="form-control" id="email" placeholder="example@gmail.com"
                            name="email" autocomplete="off" />
                    </div>
                    <div class="mb-4">
                        <label for="no_handphone" class="form-label fs-5">No. Handphone</label>
                        <input type="text" class="form-control" id="no_handphone" placeholder="0812xxxxxxxx"
                            name="no_handphone" />
                    </div>
                    <div class="mb-4">
                        <label for="defaultFormControlInput" class="form-label fs-5">Tempat Lahir</label>
                        <input type="text" class="form-control" id="tempat_lahir" placeholder="Tempat Lahir"
                            name="tempat_lahir" />
                    </div>
                    <div class="mb-4">
                        <label for="tanggal_lahir" class="col-md-2 col-form-label">Tanggal Lahir</label>
                        <div class="col-md-12">
                            <input class="form-control" type="date" name="tanggal_lahir" id="tanggal_lahir" />
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="jenis_kelamin" class="form-label fs-5">Jenis Kelamin</label>
                        <select id="jenis_kelamin" class="form-select" name="jenis_kelamin">
                            <option hidden>-- Jenis Kelamin --</option>
                            <option value="L">Laki-Laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="alamat" class="form-label fs-5">Alamat</label>
                        <input type="text" class="form-control" id="alamat" placeholder="Alamat" name="alamat"
                            autocomplete="off" />
                    </div>
                    <div class="mb-4">
                        <label for="tanggal_join" class="form-label fs-5">Tanggal Join</label>
                        <input type="date" class="form-control" id="tanggal_join" name="tanggal_join" />
                    </div>
                    <div class="mb-4">
                        <label for="jabatan" class="form-label fs-5">Jabatan</label>
                        <select id="jabatan" class="form-select" name="jabatan">
                            <option hidden>-- Jabatan --</option>
                            <option value="HR">HR</option>
                            <option value="Staff">Staff</option>
                        </select>
                    </div>

                    <button class="btn btn-warning btn-lg"><i class="bx bx-printer"></i> Cetak</button>
                </div>
            </div>
        </div>
    </div>
@endsection
