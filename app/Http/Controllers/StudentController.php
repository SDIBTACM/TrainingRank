<?php
/**
 * It have many bugs
 * Created in dreaming.
 * User: Boxjan
 * Datetime: 2019-03-16 13:39
 */

namespace App\Http\Controllers;


use App\Log;
use App\Model\ContestResult;
use App\Model\Group;
use App\Model\Student;
use DB;
use Illuminate\Support\Facades\Request;


class StudentController extends Controller
{
    public function index(\Illuminate\Http\Request $request) {

        $students = Student::with(['contestResult' => function($query) {
            $query->orderBy('contest_id', 'desc')->orderBy('rating', 'desc')->value('rating');
        }]);

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
            $students = Student::where('is_show', 0)->select(['student_id', 'name', 'id'])->paginate(20) :
            $students = $students->where('is_show', 0)->orderBy('rating', 'desc')
                ->select(['student_id', 'name', 'id', 'rating'])->paginate(20);


        return view('home.student.index', [
            'title' => 'Student List',
            'students' => $students,
            'groups' => Group::select(['id', 'name'])->get(),
            ]);
    }

    public function show($id) {

        $student = Student::where('is_show', 0)->findOrFail($id);

        $ratingsRows = ContestResult::with(['contest' => function($query) {
            $query->select('id', 'name', 'start_time');
        }])->where('student_id', $id)->orderBy('contest_id', 'asc')->select(['contest_id', 'rank', 'rating'])->get();

        $ratings = [
            'labels' => [],
            'data' => [],
        ];

        foreach ($ratingsRows as $row) {
            array_push($ratings['labels'], $row['contest'] === null ? 'null' :
                [$row['contest']->name , $row['contest']->start_time]);
            array_push($ratings['data'], $row['rating']);
        }

        Log::debug('', $ratings);

        return view('home.student.show', [
            'title' => 'Student Detail',
            'student' => $student,
            'ratings' => $ratings,
        ]);
    }

}