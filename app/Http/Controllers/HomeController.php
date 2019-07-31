<?php

namespace App\Http\Controllers;


use App\Model\Contest;
use App\Model\Group;
use App\Model\Student;

class HomeController extends Controller
{

    public function index() {

        $students = Student::where('is_show', 0)
            ->select(['id','name', 'student_id', 'rating'])->OrderBy('rating', 'desc');

        if (\Request::get('group', null) != null)
            $students = $students->where('group', \Request::get('group'));

        $students = $students->limit(20)->get();


        return view('welcome', [
            'contests' => Contest::limit(20)->OrderBy('start_time', 'desc')->get(),
            'students' => $students,
            'groups' => Group::get(),
           ]);
    }

}
