<?php

namespace App\Http\Controllers;


use App\Model\Contest;
use App\Model\ContestResult;
use App\Model\Group;
use App\Model\Student;

class HomeController extends Controller
{

    public function index() {

        $studentsRow = Student::where('is_show', 0)->select(['id','name', 'student_id']);

        if (\Request::get('group', null) != null)
            $studentsRow = $studentsRow->where('group', \Request::get('group'));

        $studentsRow = $studentsRow->get();

        $students = [];
        foreach ($studentsRow as $row) {
            array_push($students,[
                'id' => $row->id,
                'name' => $row->name,
                'student_id' => $row->student_id,
                'rating' => ContestResult::getLatestRatingByStudentId($row->id),
                ]);
        }

        usort($students, array(__CLASS__, "cmpByStudentRating"));

        return view('welcome', [
            'contests' => Contest::limit(20)->get(),
            'students' => $students,
            'groups' => Group::get(),
           ]);
    }

    private function cmpByStudentRating($a1, $a2) {
        return $a1['rating'] < $a2['rating'];
    }
}
