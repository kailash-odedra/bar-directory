<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <title>@isset($title){{ $title }}@endisset</title>
    <link rel="icon" type="image/x-icon" href="{{ Vite::asset('resources/images/favicon.ico') }}"/>
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('plugins/src/bootstrap/css/bootstrap.min.css') }}">
    
    <!-- Feather Icons -->
    <link rel="stylesheet" type="text/css" href="{{ asset('plugins/src/feather/feather.css') }}">
    
    <!-- Custom Styles -->
    <style>
        body {
            background: #f8f9fa;
            padding: 20px;
            font-family: 'Nunito', sans-serif;
        }
        .booking-container {
            max-width: 1200px;
            margin: 0 auto;
        }
    </style>
    
    @yield('styles')
</head>
<body>
    @yield('content')

    <!-- Bootstrap JS -->
    <script src="{{ asset('plugins/src/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    
    @yield('scripts')
</body>
</html>

