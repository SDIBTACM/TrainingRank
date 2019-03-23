<?php
/**
 * It have many bugs
 * Created in dreaming.
 * User: Boxjan
 * Datetime: 2019-03-16 14:52
 */

namespace App\Model;


use App\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Student extends Model
{

    static public function newStudent($name, $student_id, $isShow = 0, $group = 1) {
        $student = new self;
        $student->name = $name;
        $student->group = $group;
        $student->is_show = $isShow;
        $student->student_id = $student_id;

        $student->save();

        ContestResult::newStudent($student->id);

        Log::info('User: {}, add new student: {}', Auth::user()->username, $student);

        return $student;
    }

}