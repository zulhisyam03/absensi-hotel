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
                        <form method="post" id="formUser">
                            @csrf
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
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Ambil elemen-elemen yang diperlukan
            const form = document.getElementById('formUser');
            const btnGantiPassword = document.getElementById('btnGantiPassword');
            const newPasswordInput = document.getElementById('new-password');
            const verNewPasswordInput = document.getElementById('ver-new-password');
            const alertPassword = document.getElementById('alert-password');
            const alertVerPassword = document.getElementById('alert-ver-password');

            // Debug: Pastikan elemen ditemukan
            console.log('newPasswordInput:', newPasswordInput);
            console.log('verNewPasswordInput:', verNewPasswordInput);
            console.log('btnGantiPassword:', btnGantiPassword);

            // Jika elemen tidak ditemukan, hentikan eksekusi dan tampilkan error
            if (!newPasswordInput || !verNewPasswordInput || !btnGantiPassword) {
                console.error(
                    'Salah satu elemen form tidak ditemukan. Pastikan ID HTML cocok dan DOM sudah dimuat.');
                alert('Error: Elemen form tidak ditemukan. Periksa console untuk detail.');
                return;
            }

            // Fungsi untuk clear alert
            function clearAlerts() {
                alertPassword.textContent = '';
                alertVerPassword.textContent = '';
            }

            // Event listener untuk tombol
            btnGantiPassword.addEventListener('click', function(event) {
                event.preventDefault(); // Cegah submit form default
                clearAlerts(); // Bersihkan alert sebelumnya

                const passwordBaru = newPasswordInput.value.trim();
                const verPasswordBaru = verNewPasswordInput.value.trim();

                let isValid = true;

                // Validasi: Password baru tidak kosong dan minimal 8 karakter
                if (!passwordBaru) {
                    alertPassword.textContent = 'Password baru wajib diisi.';
                    isValid = false;
                } else if (passwordBaru.length < 8) {
                    alertPassword.textContent = 'Password baru minimal 8 karakter.';
                    isValid = false;
                }

                // Validasi: Ulangi password tidak kosong dan cocok
                if (!verPasswordBaru) {
                    alertVerPassword.textContent = 'Ulangi password baru wajib diisi.';
                    isValid = false;
                } else if (passwordBaru !== verPasswordBaru) {
                    alertVerPassword.textContent = 'Password baru dan ulangan tidak cocok.';
                    isValid = false;
                }

                // Jika validasi gagal, hentikan
                if (!isValid) return;

                // Ambil CSRF token
                const csrfMeta = document.querySelector('meta[name="csrf-token"]');
                if (!csrfMeta) {
                    console.error(
                        'CSRF token tidak ditemukan. Pastikan meta tag <meta name="csrf-token" content="{{ csrf_token() }}"> ada di HTML.'
                    );
                    alert('Error: CSRF token tidak ditemukan.');
                    return;
                }
                const csrfToken = csrfMeta.getAttribute('content');

                // Jika valid, kirim AJAX
                const formData = new FormData();
                formData.append('password_baru', passwordBaru);
                formData.append('password_baru_confirmation', verPasswordBaru);
                formData.append('_token', csrfToken); // Sertakan CSRF token

                fetch('/config/user/update', { // Ganti URL jika berbeda
                        method: 'POST',
                        body: formData,
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(data.message || 'Password berhasil diubah. Silahkan Login Kembali');
                            logoutUser();
                            // Redirect ke halaman lain, misalnya profil
                            // window.location.href = '/logout'; // Sesuaikan URL redirect
                        } else {
                            // Tangani error dari server (misalnya validasi gagal)
                            if (data.errors) {
                                if (data.errors.password_baru) {
                                    alertPassword.textContent = data.errors.password_baru[0];
                                }
                                if (data.errors.ver_password_baru) {
                                    alertVerPassword.textContent = data.errors.ver_password_baru[0];
                                }
                            } else {
                                alert('Terjadi kesalahan: ' + (data.message || 'Coba lagi.'));
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan jaringan. Coba lagi.');
                    });
            });
        });
    </script>
@endpush
