@extends('layouts/contentNavbarLayout')

@section('title', 'Basic Inputs - Forms')

@section('page-script')
    {{-- @vite('resources/assets/js/form-basic-inputs.js') --}}
@endsection

@section('content')
    <div class="row g-12">
        <div class="col-md-12">
            <div class="card">
                <h5 class="card-header"><i class="bx bx-lg bx-dollar-circle"></i> Payroll</h5>
                <div class="card-body col-12 mx-auto">
                    <div class="mb-4">
                        <label for="defaultFormControlInput" class="form-label fs-5">Nama Pegawai</label>
                        <input class="form-control" list="datalistOptions" id="exampleDataList"
                            placeholder="Type to search...">
                        <datalist id="datalistOptions">
                            <option value="San Francisco"></option>
                            <option value="New York"></option>
                            <option value="Seattle"></option>
                            <option value="Los Angeles"></option>
                            <option value="Chicago"></option>
                        </datalist>
                        {{-- <div id="defaultFormControlHelp" class="form-text">We'll never share your details with anyone else.
                        </div> --}}
                    </div>

                    <div class="mb-4">
                        <label for="defaultFormControlInput" class="form-label fs-5">Periode</label>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <select id="defaultSelect" class="form-select" name="periode_bulan">
                                    <option hidden>Bulan</option>
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
                                    <option hidden>Tahun</option>
                                    <option value="2025">2025</option>
                                    <option value="2024">2024</option>
                                    <option value="2023">2023</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="defaultFormControlInput" class="form-label fs-5">Total Hari Kerja</label>
                        <input type="number" class="form-control" id="exampleFormControlInput1"
                            placeholder="Total Hari Kerja" readonly />
                    </div>
                    <div class="mb-4">
                        <label for="defaultFormControlInput" class="form-label fs-5">Total Gaji</label>
                        <input type="text" class="form-control" id="exampleFormControlInput1"
                            placeholder="Rp. 1.000.000" />
                    </div>

                    <button class="btn btn-warning btn-lg"><i class="bx bx-printer"></i> Cetak</button>
                </div>
            </div>
        </div>
    </div>
@endsection
