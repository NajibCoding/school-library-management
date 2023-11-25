<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AdminPanel | {{ $title }}</title>
    <link rel="icon" type="image/png" href="{{ url('assets/images/favicon.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    {{-- {!! ReCaptcha::htmlScriptTagJsApi() !!} --}}

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('assets') }}/plugins/fontawesome-free/css/all.min.css">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="{{ asset('assets') }}/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('assets') }}/dist/css/adminlte.min.css">

    <link rel="stylesheet" href="{{ asset('assets') }}/toastr/toastr.min.css">

    <!-- Sweet Alert css -->
    <link href="{{ asset('assets') }}/sweet-alert/sweetalert2.min.css" rel="stylesheet" type="text/css" />
    {{-- Jika mau menggunakan ReCaptcha, jika tidak bisa di matikan terlebih dahulu --}}
    {{-- {!! ReCaptcha::htmlScriptTagJsApi() !!} --}}

</head>
