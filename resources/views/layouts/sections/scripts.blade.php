<!-- BEGIN: Vendor JS-->

@vite(['resources/assets/vendor/libs/jquery/jquery.js', 'resources/assets/vendor/libs/popper/popper.js', 'resources/assets/vendor/js/bootstrap.js', 'resources/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js', 'resources/assets/vendor/js/menu.js'])

@yield('vendor-script')
<!-- END: Page Vendor JS-->
<!-- BEGIN: Theme JS-->
@vite(['resources/assets/js/main.js'])

<!-- END: Theme JS-->
<!-- Pricing Modal JS-->
@stack('pricing-script')
<!-- END: Pricing Modal JS-->
<!-- BEGIN: Page JS-->
@yield('page-script')
<!-- END: Page JS-->

<script>
    function logoutUser() {
        fetch("{{ route('logout') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Accept": "application/json",
                },
            })
            .then(response => {
                if (response.redirected) {
                    window.location.href = response.url; // follow redirect
                } else {
                    window.location.href = "/auth/login-basic";
                }
            })
            .catch(() => alert("Logout gagal, coba lagi."));
    }

    function navigateToUserForm() {
        fetch("{{ route('config-user') }}", {
                method: "GET",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Accept": "application/json",
                },
            })
            .then(response => {
                if (response.redirected) {
                    window.location.href = response.url; // follow redirect
                } else {
                    window.location.href = "/config/user";
                }
            })
            .catch(() => alert("Navigation gagal, coba lagi."));
    }
</script>
