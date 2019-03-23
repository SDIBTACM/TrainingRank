<?php

namespace App\Http\Controllers\Auth;

use App\Log;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class RegisterController extends Controller
{

    public function show() {
        if ( DB::table('users')->count() )
            return redirect()->route('login');
        return view('auth/register');
    }

    public function register(Request $request) {
        if ( DB::table('users')->count() )
            return redirect()->route('login');

        $validatedData = $request->validate([
            'username' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $this->create($request);

        return redirect()->route('home');
    }


    private function create(Request $request)
    {
        Log::info('username: {} ip: {} register success', $request->username, $request->getClientIp());
        return User::create([
            'username' => $request->input('username'),
            'password' => Hash::make($request->input('password')),
        ]);
    }
}
