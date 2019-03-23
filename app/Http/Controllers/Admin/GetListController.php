<?php
/**
 * It have many bugs
 * Created in dreaming.
 * User: Boxjan
 * Datetime: 2019-03-22 11:06
 */

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Model\Group;
use App\Model\Student;
use Illuminate\Http\Request;

class GetListController extends Controller
{
    public function studentByGroup(Request $request) {
        if ($request->has('group')) {
            $group = Group::findOrFail($request->post('group'));

            $studentList = Student::where('group', $group->id)->select(['id','student_id','name'])->get();

            return $studentList;
        } else {
            return abort(400);
        }
    }
}