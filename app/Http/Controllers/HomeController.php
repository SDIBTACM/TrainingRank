<?php

namespace App\Http\Controllers;


use App\Model\Contest;
use App\Model\ContestResult;
use App\Model\Student;

class HomeController extends Controller
{

    public function index() {

        $students = Student::where('is_show', 0)->inRandomOrder()->select(['id','name', 'student_id'])->limit(10)->get();

        foreach ($students as $student) {
            $student->rating = ContestResult::getLatestRatingByStudentId($student->id);
        }

        return view('welcome', [
            'contests' => Contest::limit(10)->get(),
            'students' => $students
           ]);
    }
}
