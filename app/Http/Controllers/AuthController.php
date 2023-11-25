<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Mail\ResetPassword;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class AuthController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            return redirect('admin/dashboard');
        }
        return view('auth/login', ['title' => 'Login']);
    }

    public function forget_password()
    {
        if (Auth::check()) {
            return redirect('admin/dashboard');
        }
        return view('auth/forget_password', ['title' => 'Lupa Password']);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|max:255|email|exists:users,email',
            'password' => 'required|string|min:8|max:20',
            // 'g-recaptcha-response' => 'required|recaptcha'
        ], [
            // 'recaptcha' => 'Mohon Selsaikan Captcha Terlebih Dahulu'
        ], [
            // 'g-recaptcha-response' => 'Captcha'
        ]);

        if ($validator->fails()) {
            Toastr::error('Validator Error!');
            return back()->withErrors($validator)->withInput();
        }

        if (Auth::attempt([
            'email' => sanitize_string($request->email),
            'password' => sanitize_string($request->password),
            'status' => '1'
        ])) {
            $id = Auth::user()->id;
            User::find($id)->update(['last_login' => now()]);
            return redirect('admin/dashboard');
        }

        Toastr::error('Email/Password Salah');
        return back()->withInput();
    }

    public function logout(Request $request)
    {
        $request->session()->flush();
        Auth::logout();
        return redirect('auth/login');
    }

    public function change_password(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password_lama' => 'required',
            'password_baru' => 'required|string|min:8|max:20|same:re_password',
            're_password' => 'required|string|min:8|max:20|same:password_baru'
        ], [], [
            'password_lama' => 'Password Lama',
            'password_baru' => 'Password Baru',
            're_password' => 'Verifikasi Password Baru'
        ]);

        $old_password = sanitize_string($request->password_lama);
        $new_password = sanitize_string($request->password_baru);

        if ($validator->fails()) {
            return response()->json(apiRes("error_val", $validator->getMessageBag()->toArray()), 200);
        }
        if (Hash::check($old_password, auth()->user()->password)) {
            DB::beginTransaction();
            try {
                auth()->user()->update([
                    'password' => bcrypt($new_password)
                ]);
                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                // throw $e;
                return response()->json(apiRes("error_message", $e->getMessage()), 200);
            }
            return response()->json(apiRes("success", "Berhasil merubah password"), 200);
        } else {
            return response()->json(apiRes("error_message", "Data tidak sesuai dengan password lama"), 200);
        }
    }

    public function request_reset_password(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email,status,!2',
        ], [], [
            'email' => 'Alamat Email',
        ]);

        $email = sanitize_string($request->email);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $data = User::withoutDeleted()->where('email', $email);
            $data->update([
                'reset_password_token' => Str::random(150),
                'valid_reset_password_token_until' => now()->addHours(1)
            ]);

            Mail::to($email)->send(new ResetPassword($data->firstOrFail()));
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            if ($e instanceof ModelNotFoundException) {
                Toastr::error('Data tidak ditemukan');
                return back()->withInput();
            }
            Toastr::error($e->getMessage());
            return back()->withInput();
        }
        Toastr::success("Berhasil mengirimkan email reset password!");
        return redirect('auth/forget_password');
    }

    public function reset_password($reset_password_token)
    {
        $validator = Validator::make(['reset_password_token' => $reset_password_token], [
            'reset_password_token' => 'required|string|exists:users,reset_password_token,status,!2',
        ], [], [
            'reset_password_token' => 'Token Reset Password',
        ]);

        $reset_password_token = sanitize_string($reset_password_token);

        if ($validator->fails()) {
            Toastr::error('Token tidak sesuai atau sudah expired!');
            return redirect('auth/login');
        }

        try {
            $data = User::withoutDeleted()->where('reset_password_token', $reset_password_token)->firstOrFail();
            if ($data->valid_reset_password_token_until > now()->format("Y-m-d H:i:s")) {
                $viewData = array(
                    'title' => 'Reset Password',
                    'reset_password_token' => $reset_password_token,
                );
                return view('auth.reset_password', $viewData);
            } else {
                $data->update([
                    'reset_password_token' => null,
                    'valid_reset_password_token_until' => null
                ]);
            }
        } catch (\Exception $e) {
            if ($e instanceof ModelNotFoundException) {
                Toastr::error('Data tidak ditemukan');
                return back()->withInput();
            }
            Toastr::error($e->getMessage());
            return back()->withInput();
        }
    }

    public function submit_reset_password($reset_password_token, Request $request)
    {
        $input = $request->collect();
        $input->put('reset_password_token', $reset_password_token);
        $validator = Validator::make($input->toArray(), [
            'reset_password_token' => 'required|string|exists:users,reset_password_token,status,!2',
            'password' => 'required|string|same:re_password|min:8|max:20',
            're_password' => 'required|string|same:password|min:8|max:20',
        ], [], [
            'reset_password_token' => 'Token Reset Password',
            'password' => 'Password Baru',
            're_password' => 'Verifikasi Password Baru',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $password = sanitize_string($input->get('password'));

        DB::beginTransaction();
        try {
            $data = User::withoutDeleted()->where('reset_password_token', $reset_password_token)->firstOrFail();
            if ($data->valid_reset_password_token_until > now()->format("Y-m-d H:i:s")) {
                $data->update([
                    'password' => bcrypt($password),
                    'reset_password_token' => null,
                    'valid_reset_password_token_until' => null
                ]);
                Toastr::success("Password kamu berhasil diubah!");
                DB::commit();
                return redirect('auth/login');
            } else {
                $data->update([
                    'reset_password_token' => null,
                    'valid_reset_password_token_until' => null
                ]);
                Toastr::error('Token tidak sesuai atau sudah expired!');
                DB::rollBack();
                return redirect('auth/login');
            }
        } catch (\Exception $e) {
            if ($e instanceof ModelNotFoundException) {
                Toastr::error('Data tidak ditemukan');
                return back()->withInput();
            }
            Toastr::error($e->getMessage());
            return back()->withInput();
        }
    }
}
