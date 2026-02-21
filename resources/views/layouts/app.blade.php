<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Purple Admin</title>

    <!-- Plugins CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/ti-icons/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/font-awesome/css/font-awesome.min.css') }}">

    <!-- Plugin page CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">

    <!-- Layout CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">

    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}" />

    {{-- Style Page --}}
    @stack('style')
</head>

<body>

<div class="container-scroller">

    {{-- Navbar --}}
    @include('layouts.partials.navbar')

    <div class="container-fluid page-body-wrapper">

        {{-- Sidebar --}}
        @include('layouts.partials.sidebar')

        {{-- Main Panel --}}
        <div class="main-panel">
            <div class="content-wrapper">
                @yield('content')
            </div>

            {{-- Footer --}}
            @include('layouts.partials.footer')
        </div>

    </div>
</div>

<!-- JS Global -->
<script src="{{ asset('assets/vendors/js/vendor.bundle.base.js') }}"></script>
<script src="{{ asset('assets/js/off-canvas.js') }}"></script>
<script src="{{ asset('assets/js/misc.js') }}"></script>

{{-- JS Page --}}
@stack('script')

</body>
</html>