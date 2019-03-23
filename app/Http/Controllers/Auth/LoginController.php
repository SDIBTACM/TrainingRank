<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Log;
use App\User;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;


class LoginController extends Controller
{

    public function showLoginFrom() {
        if ( DB::table('users')->count() == 0)
            return redirect()->route('register');

        if ( Auth::check() )
            return redirect()->route('admin.index');

        return view('auth/login');
    }

    public function login(Request $request)
    {
        if ( DB::table('users')->count() == 0)
            return redirect()->route('register');

        $credentials = $request->only('username', 'password');

        if (Auth::attempt($credentials)) {
            return redirect()->intended(route('admin.index'));
        }

        $password_hash = DB::table('users')->where('username', $credentials['username'])->value('password');

        Log::debug('',$password_hash);

        if ( !is_null($password_hash) && $this->passwordCheck($password_hash, $credentials['password'])) {
            $user = User::where('username', $credentials['username'])->first();

            $user->password = Hash::make($credentials['password']);
            Auth::loginUsingId($user->id);
            $user->save();

            return redirect()->intended(route('admin.index'));
        }

        Log::warning("username: {}, ip: {} Login Fail!", $credentials['username'], $request->ip());
        return view('auth/login');
    }

    public function logout() {

        Auth::logout();
        return redirect('/');
    }

    private function passwordCheck($hash, $password) {
        return $hash == $password;
    }
}
