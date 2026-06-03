<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ICT Admin</title>
    <link rel="icon" href="{{ asset('images/icon.png') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap-5.3.8.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/adminlte-3.2.0.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/fontawesome-7.1.0.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sweetalert2.min.css') }}">

    <script src="{{ asset('js/sweetalert2.min.js') }}"></script>
</head>

<body class="sidebar-mini layout-fixed">
    <div class="wrapper">
        @if (session()->has('c_employee_id'))
            <x-header />
            <x-sidebar />
            <div class="content-wrapper p-3">
                {{ $slot }}
            </div>
            <x-footer />
        @else
            {{ $slot }}
        @endif
    </div>

    @if (session()->has('status') && session()->has('message'))
        <script>
            const status = '{{ session('status') }}'; // success, error, warning, info, question
            const message = '{{ session('message') }}';

            Swal.fire({
                icon: status,
                title: status.charAt(0).toUpperCase() + status.slice(1), // Kapitalisasi huruf pertama
                text: message,
                toast: true, // Opsional: gunakan sebagai toast di pojok
                position: 'top-end', // Opsional: posisi toast
                showConfirmButton: false,
                timer: 5000,
                timerProgressBar: true,
                // Anda bisa menyesuaikan opsi lain di sini
            });
        </script>
    @endif

    <script>
        const alertSuccess = document.querySelector('.alert-success')
        if (alertSuccess) {
            setTimeout(() => {
                alertSuccess.classList.add('d-none');
            }, 3000);
        }
    </script>

    <script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap-5.3.8.min.js') }}"></script>
    <script src="{{ asset('js/adminlte-3.2.0.min.js') }}"></script>
</body>

</html>
