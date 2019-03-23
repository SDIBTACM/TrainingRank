<?php
/**
 * It have many bugs
 * Created in dreaming.
 * User: Boxjan
 * Datetime: 2019-03-16 13:49
 */

namespace App\Http\Controllers\Admin;


use App\Log;
use App\Model\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController
{
    public function index() {
        return view('admin.user.index', [
            'title' => 'User manager',
            'users' => User::all(),
        ]);
    }

    public function store(Request $request) {
        $validatedData = $request->validate([
            'username' => 'required|string|max:255|unique:users,username',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = new User;
        $user->username = $request->post('username');
        $user->nickname = $request->post('nickname');
        $user->password = $request->post('password');
        $user->save();

        Log::info('username: {} Add user: {} ', Auth::user()->username, $user);

        return [
            'date' => date('Y-m-d H:m:s'),
            'id' => $user->id,
        ];

    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $student = User::find($id);
        if ($request->has('password')) {
            $validatedData =  $request->validate([
                'password' => 'required|string|min:6',
            ]);
            $student->password = $request->input('password');
        }

        if ($request->has('nickname')) {
            $student->nickname = $request->input('nickname');
        }

        $student->save();

        Log::info('User: {} update category: {} ', Auth::user()->username, $student);

    }


    public function destroy($id)
    {

        $oldStudent = User::find($id);

        Log::info('User: {} delete student: {}', Auth::user()->username, $oldStudent);

        User::destroy($id);
    }
}