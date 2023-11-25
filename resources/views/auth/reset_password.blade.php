@extends('auth._layout')


@section('container')
    <div class="login-box">
        <div class="login-logo mb-2">
            <a href="{{ url('/') }}"><b>Admin</b>Panel</a>
        </div>
        <!-- /.login-logo -->
        <div class="card">
            <div class="card-body login-card-body">
                <p class="login-box-msg">Reset Password</p>
                <div id="validation-errors"></div>
                <form method="POST">
                    @csrf

                    <div class="form-group input-group">
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                            value="{{ old('password') }}" placeholder="Password Baru">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                        @error('password')
                            <small class="from-text text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group input-group">
                        <input type="password" name="re_password"
                            class="form-control @error('re_password') is-invalid @enderror" value="{{ old('re_password') }}"
                            placeholder="Masukkan Ulang Password Baru">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                        @error('re_password')
                            <small class="from-text text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- <div class="input-group @error('g-recaptcha-response') is-invalid @enderror" style="margin-top:15px;">
                        {!! htmlFormSnippet() !!}
                    </div>
                    @error('g-recaptcha-response')
                        <small class="from-text text-danger">{{ $message }}</small>
                    @enderror --}}

                    <div class="row" style="margin-top:15px;">
                        <div class="col-8">

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
