<!DOCTYPE html>
<html lang="en">
@include('admin.layout.head')

<body class="hold-transition sidebar-mini text-sm">
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>

            </ul>

            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">
                <!-- Notifications Dropdown Menu -->
                <li class="nav-item dropdown">
                    <a class="nav-link" data-toggle="dropdown" href="#">

                        {{-- {{ Auth::user()->fullname }} --}}
                        <i class="fa fa-caret-down"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                        <a href="javascript:void(0)" onclick="ubahPassword({{-- Auth::user()->id --}});"
                            class="dropdown-item">
                            <i class="fas fa-unlock"></i> Ubah Password

                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="{{ url('admin/logout') }}" class="dropdown-item">
                            <i class="fas fa-times "></i> Log Out

                        </a>

                </li>
                <li class="nav-item">
                    <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                        <i class="fas fa-expand-arrows-alt"></i>
                    </a>
                </li>

            </ul>
        </nav>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-light-danger elevation-4">
            <!-- Brand Logo -->
            <a href="{{ url('/') }}" class="brand-link">
                @if (isset($setting_logo_website) && $setting_logo_website)
                    <img src="{{ asset('assets/images') }}/logo.png" class="brand-image " style="opacity: .8">
                    <span class="brand-text"> | Admin Panel</span>
                @else
                    <span class="brand-text">{{ isset($setting_title_website) ? $setting_title_website : null }} | Admin Panel</span>
                @endif
            </a>

            <!-- Sidebar -->
            <div class="sidebar">

                <!-- Sidebar Menu -->
                {{-- {{ var_dump(getMenus(Auth::user()->role_id)); }} --}}
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                        data-accordion="false">
                        {!! getMenus() !!}
                    </ul>
                </nav>
                <!-- /.sidebar-menu -->
            </div>
            <!-- /.sidebar -->
        </aside>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>{{ $page_header }}</h1>
                        </div>
                        {{-- <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item active">DataTables</li>
              </ol>
            </div> --}}
                    </div>
                </div><!-- /.container-fluid -->
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">

                            @yield('container')


                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->
                </div>
                <!-- /.container-fluid -->
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
        </aside>
        <!-- /.control-sidebar -->
        @include('admin.layout.footer')
    </div>
    <!-- ./wrapper -->

    {{-- modal cp --}}
    <div class="modal fade" tabindex="-1" role="dialog" id="ModalChangePassword">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal_title_cp"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" id="formChangePassword">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="col-form-label">Password lama</label>
                            <input type="password" class="form-control" name="password_lama" id="password_lama"
                                value="">
                            <small class="form-text text-danger" id="password_lama_error"></small>
                        </div>

                        <div class="form-group">
                            <label class="col-form-label">Password Baru</label>
                            <input type="password" class="form-control" name="password_baru" id="password_baru"
                                value="">
                            <small class="form-text text-danger" id="password_baru_error"></small>
                        </div>

                        <div class="form-group">
                            <label class="col-form-label">Ketik Ulang Password</label>
                            <input type="password" class="form-control" name="re_password" id="re_password"
                                value="">
                            <small class="form-text text-danger" id="re_password_error"></small>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Ubah</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    </div>
            </div>
            </form>
        </div>
    </div>
    @include('admin.layout.script')


</body>

</html>
