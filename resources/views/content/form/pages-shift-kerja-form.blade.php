@extends('layouts/contentNavbarLayout')

@section('title', 'Shift Kerja - Forms')

@section('page-script')
    {{-- @vite('resources/assets/js/form-basic-inputs.js') --}}
@endsection

@section('content')
    <div class="row g-12">
        <div class="col-md-12">
            <div class="card">
                <h5 class="card-header"><i class="bx bx-lg bx-timer"></i> Form Shift Kerja / Tambah Data</h5>
                <div class="card-body col-12 mx-auto">
                    <div class="mb-4">
                        <label for="defaultFormControlInput" class="form-label fs-5">Nama Pegawai</label>
                        <input class="form-control" list="datalistOptions" id="exampleDataList"
                            placeholder="Type to search..." name="nama_pegawai" />
                        <datalist id="datalistOptions">
                            <option value="Rendy"></option>
                            <option value="Ana"></option>
                            <option value="Seli"></option>
                            <option value="Andi"></option>
                            <option value="Rizz"></option>
                        </datalist>
                    </div>
                    <div class="mb-4">
                        <label for="jenis_kelamin" class="form-label fs-5">Shift Kerja</label>
                        <select id="jenis_kelamin" class="form-select" name="jenis_kelamin">
                            <option hidden>-- Shift Kerja --</option>
                            <option value="malam">Malam</option>
                            <option value="pagi">Pagi</option>
                            <option value="siang">Siang</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="waktu-masuk" class="form-label fs-5">Waktu Masuk</label>
                        <input type="time" class="form-control" id="waktu-masuk" name="waktu_masuk" autocomplete="off" />
                    </div>
                    <div class="mb-4">
                        <label for="waktu-pulang" class="form-label fs-5">Waktu Pulang</label>
                        <input type="time" class="form-control" id="waktu-pulang" name="waktu_pulang" />
                    </div>

                    <button class="btn btn-primary btn-lg w-100"><i class="bx bx-save"></i> Simpan</button>
                </div>
            </div>
        </div>
    </div>
@endsection
