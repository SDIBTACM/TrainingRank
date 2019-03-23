<?php
/**
 * It have many bugs
 * Created in dreaming.
 * User: Boxjan
 * Datetime: 2019-03-21 20:16
 */

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Log;
use App\Model\Group;
use App\Model\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
    public function index(){
        return view('admin.group.index', [
            'title' => 'Group Manager',
            'groups' => Group::get()
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|unique:groups,name',
        ]);

        $group = new Group;
        $group->name = $validatedData['name'];
        $group->save();

        Log::info('User: {} add a Group, name: {}', Auth::user()->username, $group);
        return [
            'date' => date('Y-m-d H:m:s'),
            'id' => $group->id,
        ];
    }

    public function destroy($id)
    {
        if ($id == 1) {
            abort(400);
            Log::warning("User try to delete Group 1", Auth::user()->username);
        }

        $groups = Group::findOrFail($id);
        Student::where('group', $id)->update(['group' => 1]);
        Log::info('User: {} delete group: {}', Auth::user()->username, $groups);
        Group::destroy($id);

    }

}