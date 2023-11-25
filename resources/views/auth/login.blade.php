@extends('auth._layout')


@section('container')
    <div class="login-box">
        <div class="login-logo mb-2">
            <a href="{{ url('/') }}"><b>Admin</b>Panel</a>
        </div>
        <!-- /.login-logo -->
        <div class="card">
            <div class="card-body login-card-body">
                <p class="login-box-msg">Login Admin Panel</p>
                <div id="validation-errors"></div>
                <form method="POST" action="{{ url('auth/login') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="input-group">
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email') }}" placeholder="Email">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                    </div>
                    @error('email')
                        <small class="from-text text-danger">{{ $message }}</small>
                    @enderror

                    <div class="input-group" style="margin-top:15px;">
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                            placeholder="Password" value="{{ old('password', isset($res) ? $res->password : '') }}">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>

                    </div>
                    @error('password')
                        <small class="from-text text-danger">{{ $message }}</small>
                    @enderror
                    {{--
                    <div class="input-group @error('g-recaptcha-response') is-invalid @enderror" style="margin-top:15px;">
                        {!! htmlFormSnippet() !!}
                    </div>
                    @error('g-recaptcha-response')
                        <small class="from-text text-danger">{{ $message }}</small>
                    @enderror --}}

                    <div class="row" style="margin-top:15px;">
                        <div class="col-8">
                            <a class="align-middle" href="{{ url('auth/forget_password') }}">Lupa password <i class="fa fa-chevron-right"></i> </a>
                        </div>
                        <!-- /.col -->
                        <div class="col-12 mt-2">
                            <button type="submit" class="btn btn-danger btn-block btn-sm"><b>Sign In</b></button>
                        </div>
                        <!-- /.col -->
                    </div>
                </form>




            </div>
            <!-- /.login-card-body -->
        </div>
    </div>
    <!-- /.login-box -->
@endsection
