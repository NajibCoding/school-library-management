<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ isset($setting_title_website) ? $setting_title_website : null }} Admin | {{ $page_title }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="title"
        content="{{ isset($setting_title_website) ? $setting_title_website : null }} Admin | {{ $page_title }}">
    <meta name="description" content="{{ isset($setting_description_website) ? $setting_description_website : null }}">
    <meta name="keywords" content="{{ isset($setting_keywords_website) ? $setting_keywords_website : null }}">
    <meta name="canonical" content="{{ url()->current() }}">


    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ isset($setting_title_website) ? $setting_title_website : null }}">
    <meta property="og:description"
        content="{{ isset($setting_description_website) ? $setting_description_website : null }}">
    <meta property="og:image"
        content="{{ isset($setting_logo_website) ? url(implode('/', ['storage', $setting_logo_website])) : null }}">

    <meta property="twitter:card" content="{{ isset($setting_title_website) ? $setting_title_website : null }}">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title"
        content="{{ isset($setting_title_website) ? $setting_title_website : null }} Admin | {{ $page_title }}">
    <meta property="twitter:description"
        content="{{ isset($setting_description_website) ? $setting_description_website : null }}">
    <meta property="twitter:image"
        content="{{ isset($setting_logo_website) ? url(implode('/', ['storage', $setting_logo_website])) : null }}">

    <!-- Favicons -->
    @if ($setting_favicon_website)
        <link href="{{ url(implode('/', ['storage', $setting_favicon_website])) }}" rel="icon">
    @endif
    @if ($setting_apple_touch_favicon_website)
        <link href="{{ url(implode('/', ['storage', $setting_apple_touch_favicon_website])) }}" rel="apple-touch-icon">
    @endif

    <!-- Font Awesome -->
    {{-- <link rel="stylesheet" href="{{asset('assets')}}/plugins/fontawesome-free/css/all.min.css"> --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">

    <!-- summernote -->
    {{-- <link rel="stylesheet" href="{{asset('assets')}}/plugins/summernote/summernote-bs4.min.css">
  <link rel="stylesheet" href="{{asset('assets/plugins/summernote/summernote-list-styles-bs4.css')}}"> --}}
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">


    <!-- Upload Image CSS -->
    <link rel="stylesheet" href="{{ asset('assets') }}/css/img.css">

    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('assets') }}/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet"
        href="{{ asset('assets') }}/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">

    <!-- Toaster -->
    <link rel="stylesheet" href="{{ asset('assets') }}/toastr/toastr.min.css">

    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('assets') }}/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="{{ asset('assets') }}/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">

    <!-- Sweet Alert css -->
    <link href="{{ asset('assets') }}/sweet-alert/sweetalert2.min.css" rel="stylesheet" type="text/css" />

    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('assets') }}/dist/css/adminlte.min.css">

    <!-- daterange picker -->
    <link rel="stylesheet" href="{{ asset('assets') }}/plugins/daterangepicker/daterangepicker.css">
    <!-- Tempusdominus Bbootstrap 4 -->
    <link rel="stylesheet"
        href="{{ asset('assets') }}/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">

    <!-- jQuery -->
    <script src="{{ asset('assets') }}/plugins/jquery/jquery.min.js"></script>

    <!-- Jquery Ui -->
    <script src="{{ asset('assets') }}/plugins/jquery-ui/jquery-ui.js"></script>
    <link rel="stylesheet" href="{{ asset('assets') }}/plugins/jquery-ui/jquery-ui.css">

    {{-- timepicker --}}
    <script src="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">


    {{-- datetimepicker --}}
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.full.min.js" > </script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.min.css"> --}}


    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }

        .scroll-x-table {
            overflow-x: auto;
        }

        /* .table{ */
        /* font-size:13.5px !important; */
        /* } */
        .card {
            font-size: 13.5px !important;
        }

        .btn-sm {
            padding: 2px 10px;
        }

        .btn-group .btn-sm {
            padding: 0px 10px;
        }

        .btn-sm span {
            font-size: 13px;
        }

        .modal {
            overflow: auto !important;
        }

        /* forced sidebar child menu to red */
        .nav-treeview>.nav-item>.nav-link.active {
            background: #dc3545 !important;
        }

        /* forced pagination button to red */
        div.dataTables_wrapper div.dataTables_paginate ul.pagination li.page-item.active a.page-link {
            background: red !important;
            border-color: red !important;
            color: white !important;
        }

        .dropdown-menu.show {
            min-width: inherit;
            display: inline-block;
        }

        .form-control-sm {
            height: calc(1.8125rem + 0px);
            /* padding: .25rem .5rem; */
            font-size: .8rem;
            /* line-height: 1.5; */
            border-radius: .2rem;
        }

        select.form-control-sm~.select2-container--default {
            font-size: .8rem;
            /* height: calc(1.8125rem + 0px); */
            height: max-content;
        }
    </style>

    @stack('css')
</head>
