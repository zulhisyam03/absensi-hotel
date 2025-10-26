@extends('layouts/blankLayout')

@section('title', 'Reset Password - Pages')

@section('page-style')
    @vite(['resources/assets/vendor/scss/pages/page-auth.scss'])
@endsection

@section('content')
    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner">
                <!-- Reset Password -->
                <div class="card px-sm-6 px-0">
                    <div class="card-body">
                        <!-- Logo -->
                        <div class="app-brand justify-content-center mb-6">
                            <a href="{{ url('/') }}" class="app-brand-link gap-2">
                                <span class="app-brand-logo demo">@include('_partials.macros', [
                                    'width' => 25,
                                    'withbg' => 'var(--bs-primary)',
                                ])</span>
                                <span
                                    class="app-brand-text demo text-heading fw-bold">{{ config('variables.templateName') }}</span>
                            </a>
                        </div>
                        <!-- /Logo -->
                        <h4 class="mb-1">Reset Password 🔒</h4>
                        <p class="mb-6">Masukkan password baru Anda</p>
                        <form id="resetPasswordForm">
                            @csrf
                            <input type="hidden" name="token" value="{{ request('token') }}">
                            <input type="hidden" name="email" value="{{ request('email') }}">
                            <div class="mb-3">
                                <label for="password" class="form-label">Password Baru</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Ulangi Password Baru</label>
                                <input type="password" class="form-control" id="password_confirmation"
                                    name="password_confirmation" required>
                            </div>
                            <button id="btnReset" class="btn btn-primary">Update Password</button>
                        </form>
                        <!-- Alert untuk sukses (ditampilkan via JavaScript) -->
                        <div id="alertSuccess" class="alert alert-success mt-3" style="display: none;">
                            Password berhasil diupdate. Anda akan diarahkan ke login.
                        </div>
                        <div id="alertError" class="alert alert-danger mt-3" style="display: none;"></div>
                        <div class="text-center mt-4">
                            <a href="{{ url('auth/login-basic') }}" class="d-flex justify-content-center">
                                <i class="bx bx-chevron-left scaleX-n1-rtl me-1"></i>
                                Back to login
                            </a>
                        </div>
                    </div>
                </div>
                <!-- /Reset Password -->
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btnReset = document.getElementById('btnReset');
            const form = document.getElementById('resetPasswordForm');
            const alertSuccess = document.getElementById('alertSuccess');
            const alertError = document.getElementById('alertError');
            const csrfToken = document.querySelector('meta[name="csrf-token"]');

            if (btnReset) {
                btnReset.addEventListener('click', function(e) {
                    e.preventDefault();
                    console.log('Reset password clicked - using AJAX');

                    alertSuccess.style.display = 'none';
                    alertError.style.display = 'none';

                    const formData = new FormData();
                    formData.append('token', document.querySelector('input[name="token"]').value);
                    formData.append('email', document.querySelector('input[name="email"]').value);
                    formData.append('password', document.getElementById('password').value);
                    formData.append('password_confirmation', document.getElementById(
                        'password_confirmation').value);
                    formData.append('_token', csrfToken ? csrfToken.getAttribute('content') : '');

                    btnReset.disabled = true;
                    btnReset.textContent = 'Updating...';

                    fetch('{{ route('password.update') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken ? csrfToken.getAttribute('content') : '',
                            },
                            body: formData
                        })
                        .then(response => {
                            console.log('Response status:', response.status);
                            const contentType = response.headers.get('content-type');
                            if (contentType && contentType.includes('application/json')) {
                                return response.json().then(data => ({
                                    status: response.status,
                                    data,
                                    isJson: true
                                }));
                            } else {
                                return response.text().then(text => ({
                                    status: response.status,
                                    data: text,
                                    isJson: false
                                }));
                            }
                        })
                        .then(({
                            status,
                            data,
                            isJson
                        }) => {
                            if (status === 200 && isJson && data.success) {
                                alertSuccess.style.display = 'block';
                                setTimeout(() => {
                                    window.location.href = '{{ url('auth/login-basic') }}';
                                }, 2000);
                            } else {
                                let errorMsg = 'Terjadi kesalahan.';
                                if (isJson) {
                                    errorMsg = data.message || 'Terjadi kesalahan.';
                                } else {
                                    if (status === 419) {
                                        errorMsg = 'CSRF token tidak valid. Silakan refresh halaman.';
                                    } else if (status >= 500) {
                                        errorMsg = 'Kesalahan server internal.';
                                    } else {
                                        errorMsg = `Kesalahan HTTP ${status}.`;
                                    }
                                }
                                alertError.textContent = errorMsg;
                                alertError.style.display = 'block';
                            }
                        })
                        .catch(error => {
                            console.error('Fetch error:', error);
                            alertError.textContent = 'Terjadi kesalahan saat mengirim request: ' + error
                                .message;
                            alertError.style.display = 'block';
                        })
                        .finally(() => {
                            btnReset.disabled = false;
                            btnReset.textContent = 'Update Password';
                        });
                });
            }
        });
    </script>
@endpush
