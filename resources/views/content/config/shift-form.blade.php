@extends('layouts/contentNavbarLayout')

@section('title', 'Parameter Shift Kerja - Forms')

@section('page-script')
    {{-- @vite('resources/assets/js/form-basic-inputs.js') --}}
@endsection

@section('content')
    <div class="row g-12">
        <div class="col-md-12">
            <div class="card">
                <h5 class="card-header"><i class="bx bx-lg bx-timer"></i> Form Parameter Shift Kerja / {{ $flag }}
                    Data</h5>
                <div class="card-body col-12 mx-auto">
                    <div class="mb-4">
                        <label for="shift" class="form-label fs-5">Shift Kerja</label>
                        <input type="text" class="form-control" id="shift" name="shift" autocomplete="off"
                            value="{{ isset($data) ? $data->val : '' }}" readonly />
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
