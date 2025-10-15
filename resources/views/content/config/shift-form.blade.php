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

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible mx-4" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                        </button>
                    </div>
                @endif

                <form method="post"
                    action="{{ isset($data) ? route('config-shift-kerja.update', $data->val) : route('config-shift-kerja.store') }}">
                    @csrf
                    {{-- Handle Update Form --}}
                    @if (isset($data))
                        @method('PUT')
                    @endif
                    {{-- END Handle Update Form --}}
                    <div class="card-body col-12 mx-auto">
                        <div class="mb-4">
                            <label for="shift" class="form-label fs-5">Shift Kerja</label>
                            <input type="text"
                                class="form-control text-uppercase {{ isset($data) ? 'bg-secondary bg-opacity-25' : '' }}"
                                id="shift" name="shift" autocomplete="off"
                                value="{{ old('shift', isset($data) ? $data->val : '') }}"
                                {{ isset($data) ? 'readonly' : '' }} />
                        </div>
                        <div class="mb-4">
                            <label for="waktu-masuk" class="form-label fs-5">Waktu Masuk</label>
                            <input type="time" class="form-control" id="waktu-masuk" name="waktu_masuk"
                                autocomplete="off"
                                value="{{ old('waktu_masuk', isset($data) ? $data->waktu_masuk : '') }}" />
                        </div>
                        <div class="mb-4">
                            <label for="waktu-pulang" class="form-label fs-5">Waktu Pulang</label>
                            <input type="time" class="form-control" id="waktu-pulang" name="waktu_pulang"
                                value="{{ old('waktu_pulang', isset($data) ? $data->waktu_pulang : '') }}" />
                        </div>

                        <button class="btn btn-primary btn-lg w-100"><i class="bx bx-save"></i> Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
