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
                                    <!-- Preview Gambar -->
                                    <div class="mb-3 text-center">
                                        <div class="d-flex justify-content-center">
                                            <img id="preview-image"
                                                src="{{ Auth::user()->pegawai->foto ? '/storage/foto_pegawai/' . Auth::user()->pegawai->foto : '/assets/img/avatars/user.png' }}"
                                                alt="Preview Foto" class="img-thumbnail rounded"
                                                style="width: 200px; height: 200px; object-fit: cover;">
                                        </div>
                                        <input class="form-control mt-3 mb-2 col-3" type="file" id="foto"
                                            name="foto" accept="image/*">
                                        <button class="btn btn-primary btn-md w-100" id="btnGantiFoto"><i
                                                class="bx bx-save"></i> Upload Foto</button>
                                        <hr class="my-6">
                                    </div>
                                </div>
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
                                <div class="mb-4">
                                    <label for="last_salary" class="form-label fs-5">Gaji</label>
                                    <input class="form-control bg-secondary bg-opacity-25" type="text" id="last_salary"
                                        name="last_salary" value="{{ Auth::user()->pegawai->last_salary }}" readonly />
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

            // Fungsi helper untuk format ke Rupiah
            function formatRupiah(angka) {
                if (!angka) return '-';
                return 'Rp. ' + parseInt(angka)
                    .toString()
                    .replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }
            
            const input = document.getElementById('last_salary');
            const nilaiAwal = input.value;
            input.value = formatRupiah(nilaiAwal);

            // Ambil elemen-elemen yang diperlukan
            const form = document.getElementById('formUser');
            const btnGantiPassword = document.getElementById('btnGantiPassword');
            const newPasswordInput = document.getElementById('new-password');
            const verNewPasswordInput = document.getElementById('ver-new-password');
            const alertPassword = document.getElementById('alert-password');
            const alertVerPassword = document.getElementById('alert-ver-password');
            const btnGantiFoto = document.getElementById('btnGantiFoto');

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

            // Event Listener Ganti Foto
            btnGantiFoto.addEventListener('click', function(event) {
                event.preventDefault();
                clearAlerts();

                const inputFoto = document.getElementById('foto');
                if (!inputFoto.files.length) {
                    alert('Silakan pilih foto terlebih dahulu.');
                    return;
                }
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
                formData.append('foto', inputFoto.files[0]);
                formData.append('_token', csrfToken); // Sertakan CSRF token

                // 🔹 Tampilkan loading
                btnGantiFoto.innerHTML = 'Mengunggah...';

                fetch('/config/user/update-foto', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: formData
                    })
                    .then(response => {
                        const contentType = response.headers.get('content-type');
                        if (!response.ok) throw new Error('HTTP error ' + response.status);

                        if (contentType && contentType.includes('application/json')) {
                            return response.json();
                        } else {
                            throw new Error(
                                'Response bukan JSON, kemungkinan redirect atau error CSRF');
                        }
                    })
                    .then(data => {
                        if (data.success) {
                            alert(data.message);
                            document.getElementById('preview-image').src = data.foto_url; // update foto
                            window.location = '/config/user'
                        } else {
                            alert('Gagal: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan jaringan. Coba lagi.');
                    });

            })
            // END Event Listener Ganti Foto

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

                // 🔹 Tampilkan loading
                btnGantiPassword.innerHTML = 'Processing...';
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
