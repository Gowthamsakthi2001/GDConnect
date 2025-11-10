
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="{{ admin_asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ nanopkg_asset('vendor/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="{{ nanopkg_asset('vendor/toastr/build/toastr.min.js') }}"></script>

<script>
    function confirmLogout() {
        Swal.fire({
            title: 'Are you sure?',
            text: "You will be logged out!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, logout!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('logout-form').submit();
            }
        });
    }
</script>