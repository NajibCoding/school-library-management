@extends('auth._layout')


@section('container')
    <div class="login-box">
        <div class="login-logo mb-2">
            <a href="{{ url('/') }}"><b>Admin</b>Panel</a>
            {{-- <img src="{{ url('assets/images/inamikro_small.png') }}" style="width:100%;"> --}}
        </div>
        <!-- /.login-logo -->
        <div class="card">
            <div class="card-body login-card-body">
                <p class="login-box-msg">Forget Password</p>
                <div id="validation-errors"></div>
                <form method="POST" action="{{ url('auth/request_reset_password') }}">
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
                    {{--
                    <div class="input-group @error('g-recaptcha-response') is-invalid @enderror" style="margin-top:15px;">
                        {!! htmlFormSnippet() !!}
                    </div>
                    @error('g-recaptcha-response')
                        <small class="from-text text-danger">{{ $message }}</small>
                    @enderror --}}

                    <div class="row" style="margin-top:15px;">
                        <div class="col-8">
                            <div class="col-8">
                                <a class="align-middle" href="{{ url('auth/login') }}"><i class="fa fa-chevron-left"></i> Kembali</a>
                            </div>
                        </div>
                        <!-- /.col -->
                        <div class="col-12 mt-2">
                            <button type="submit" class="btn btn-danger btn-block btn-sm"><b>Reset Password</b></button>
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
