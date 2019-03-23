<?php
/**
 * It have many bugs
 * Created in dreaming.
 * User: Boxjan
 * Datetime: 2019-03-16 13:53
 */

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Log;
use App\Model\ContestResult;
use App\Model\Group;
use App\Model\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $students = null;

        if ($request->get('isShow', -1) != -1) {
            $students === null ?
                $students = Student::where('is_show', $request->get('isShow')) :
                $students = $students->where('is_show', $request->get('isShow'));
        }

        if ($request->get('name', null) != null) {
            $students === null ?
                $students = Student::where('name', 'like', '%' . $request->get('name') . '%') :
                $students = $students->where('name', 'like', '%' . $request->get('name') . '%');
        }

        if ($request->get('group', null) != null) {
            $students === null ?
                $students = Student::where('group', $request->get('group')) :
                $students = $students->where('group', $request->get('group'));
        }

        $students === null ?
            $students = Student::paginate(15) :
            $students = $students->paginate(15);


        return view('admin.student.index', [
            'title' => 'student manager',
            'students' => $students,
            'groups' => Group::select(['id', 'name'])->get(),
        ]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return mixed
     */
    public function store(Request $request)
    {

        $validatedData =  $request->validate([
            'name' => 'max:14|required',
            'group' => 'exists:groups,id',
            'student_id' => 'required|integer'
        ]);

        $student = Student::newStudent($validatedData['name'], $validatedData['student_id'],
            $request->has('isShowSwitch') ? 0 : 1, $validatedData['group']);

        return [
            'date' => date('Y-m-d H:m:s'),
            'id' => $student->id,
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
        $student = Student::find($id);
        if ($request->has('name')) {
            $validatedData =  $request->validate([
                'name' => 'max:14'
            ]);
            $student->name = $validatedData['name'];
        }

        if ($request->has('group')) {
            $validatedData =  $request->validate([
                'group' => 'exists:groups,id'
            ]);
            $student->group = $validatedData['group'];
        }

        if ($request->has('student_id')) {
            $validatedData =  $request->validate([
                'student_id' => 'required'
            ]);
            $student->student_id = $validatedData['student_id'];
        }

        if ($request->has('is_show')) {
            $student->is_show = $request->input('is_show') % 2;
        }

        $student->save();

        Log::info('User: {} update category: {} ', Auth::user()->username, $student);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $oldStudent = Student::find($id);

        ContestResult::where('student_id', $id)->delete();

        Log::info('User: {} delete student: {}', Auth::user()->username, $oldStudent);

        Student::destroy($id);
    }
}