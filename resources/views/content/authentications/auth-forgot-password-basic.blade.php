@extends('layouts/blankLayout')

@section('title', 'Forgot Password - Pages')

@section('page-style')
    @vite(['resources/assets/vendor/scss/pages/page-auth.scss'])
@endsection

@section('content')
    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner">

                <!-- Forgot Password -->
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
                        <h4 class="mb-1">Forgot Password? 🔒</h4>
                        <p class="mb-6">Enter your email and we'll send you instructions to reset your password</p>
                        <form id="forgotPasswordForm">
                            @csrf
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <button id="btnReset" class="btn btn-primary">Kirim Link Reset</button>
                        </form>
                        <!-- Alert untuk sukses (ditampilkan via JavaScript) -->
                        <div id="alertSuccess" class="alert alert-success mt-3" style="display: none;">
                            Link reset password telah dikirim ke email Anda.
                        </div>
                        <!-- Alert untuk error (ditampilkan via JavaScript) -->
                        <div id="alertError" class="alert alert-danger mt-3" style="display: none;"></div>
                        <div class="text-center mt-4">
                            <a href="{{ url('auth/login-basic') }}" class="d-flex justify-content-center">
                                <i class="bx bx-chevron-left scaleX-n1-rtl me-1"></i>
                                Back to login
                            </a>
                        </div>
                    </div>
                </div>
                <!-- /Forgot Password -->
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btnReset = document.getElementById('btnReset');
            const form = document.getElementById('forgotPasswordForm');
            const alertSuccess = document.getElementById('alertSuccess');
            const alertError = document.getElementById('alertError');
            const csrfToken = document.querySelector('meta[name="csrf-token"]');

            if (btnReset) {
                btnReset.addEventListener('click', function(e) {
                    e.preventDefault(); // Prevent default form submission
                    console.log('Submit clicked - using AJAX');

                    // Hide previous alerts
                    alertSuccess.style.display = 'none';
                    alertError.style.display = 'none';

                    const formData = new FormData();
                    formData.append('email', document.getElementById('email').value);
                    formData.append('_token', csrfToken ? csrfToken.getAttribute('content') : '');

                    // Disable button to prevent double-clicks
                    btnReset.disabled = true;
                    btnReset.textContent = 'Sending...';

                    // Kirim via AJAX
                    fetch('{{ route('password.email') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken ? csrfToken.getAttribute('content') : '',
                                // Remove Content-Type for FormData
                            },
                            body: formData
                        })
                        .then(response => {
                            console.log('Response status:', response.status);
                            console.log('Response headers:', response.headers.get(
                                'content-type')); // Debug header

                            // Check if response is JSON
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
                                // Tampilkan alert sukses
                                alertSuccess.textContent = data.message;
                                alertSuccess.style.display = 'block';
                                // Reset form
                                form.reset();
                            } else {
                                // Handle errors (JSON or non-JSON)
                                let errorMsg = 'Terjadi kesalahan.';
                                if (isJson) {
                                    errorMsg = data.message || (data.errors && data.errors.email ? data
                                        .errors.email[0] : 'Terjadi kesalahan.');
                                } else {
                                    // Non-JSON response (e.g., HTML error page)
                                    if (status === 419) {
                                        errorMsg =
                                            'CSRF token tidak valid. Silakan refresh halaman dan coba lagi.';
                                    } else if (status === 404) {
                                        errorMsg = 'Route tidak ditemukan. Periksa konfigurasi.';
                                    } else if (status >= 500) {
                                        errorMsg = 'Kesalahan server internal. Coba lagi nanti.';
                                    } else {
                                        errorMsg =
                                            `Kesalahan HTTP ${status}: ${data.substring(0, 200)}...`; // Truncate HTML for display
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
                            // Re-enable button
                            btnReset.disabled = false;
                            btnReset.textContent = 'Kirim Link Reset';
                        });
                });
            }
        });
    </script>
@endpush
