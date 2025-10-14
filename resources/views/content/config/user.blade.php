@extends('layouts/contentNavbarLayout')

@section('title', 'My Profile')

@section('page-style')
    {{-- Custom CSS untuk peta, marker Boxicons, dan circle radius --}}
@endsection

@section('content')
    <div class="row g-0">
        <div class="col-12 col-xxl-12 order-2 order-md-3 order-xxl-2 mb-6">
            <div class="card">
                <div class="row row-bordered g-0">
                    <div class="col-lg-12">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <div class="card-title mb-0">
                                <h5 class="m-0 me-2"><i class="bx bx-lg bx-user"></i> My Profile</h5>
                            </div>
                        </div>

                        <div class="card-body col-12 mx-auto">
                            <div class="mb-4">
                                <label for="no-pegawai" class="form-label fs-5">Nomor Pegawai</label>
                                <input class="form-control bg-secondary bg-opacity-25" type="text" id="no-pegawai"
                                    name="nomor_pegawai" value="{{ Auth::user()->pegawai->no_pegawai }}" readonly />
                            </div>
                            <div class="mb-4">
                                <label for="nama-pegawai" class="form-label fs-5">Nama Pegawai</label>
                                <input class="form-control bg-secondary bg-opacity-25" type="text" id="nama-pegawai"
                                    name="nama-pegawai" value="{{ Auth::user()->pegawai->nama_pegawai }}" readonly />
                            </div>
                            <div class="mb-4">
                                <label for="email" class="form-label fs-5">Email</label>
                                <input class="form-control bg-secondary bg-opacity-25" type="text" id="email"
                                    name="email" value="{{ Auth::user()->pegawai->email }}" readonly />
                            </div>

                            <hr class="my-6">

                            <form method="get" id="formUser">
                                @csrf
                                <div class="mb-4">
                                    <label for="new-password" class="form-label fs-5">Password Baru</label>
                                    <input class="form-control" type="password" id="new-password" name="password_baru" />
                                    <span class="text-danger" id="alert-password"></span>
                                </div>
                                <div class="mb-4">
                                    <label for="ver-new-password" class="form-label fs-5">Ulangi Password Baru</label>
                                    <input class="form-control" type="password" id="ver-new-password"
                                        name="ver_password_baru" />
                                    <span class="text-danger" id="alert-ver-password"></span>
                                </div>
                                <button class="btn btn-primary btn-md w-100" id="btnGantiPassword"><i
                                        class="bx bx-save"></i> Ganti
                                    Password</button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    </div>
@endsection

@push('scripts')
    <script>
        window.addEventListener('load', function() {
            const btnChangePassword = document.getElementById('btnGantiPassword');
            const formUser = document.getElementById('formUser');

            btnChangePassword.addEventListener('click', function(e) {
                e.preventDefault();
                var password = document.getElementById('new-password').value;
                var verPassword = document.getElementById('ver-new-password').value;
                var lengthPassword = password.length;

                if (lengthPassword < 8) {
                    document.getElementById('alert-ver-password').innerText = "";
                    document.getElementById('alert-password').innerText =
                        "Password minimal 8 karakter!";
                    return false;
                } else if (password !== verPassword) {
                    document.getElementById('alert-password').innerText = "";

                    document.getElementById('alert-ver-password').innerText = "Password tidak sesuai!";
                    return false;
                }

            });
        })
    </script>
@endpush
