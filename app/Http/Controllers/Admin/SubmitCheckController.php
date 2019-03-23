<?php
/**
 * It have many bugs
 * Created in dreaming.
 * User: Boxjan
 * Datetime: 2019-03-16 20:32
 */

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Log;
use App\Model\Student;
use Illuminate\Http\Request;

class SubmitCheckController extends Controller
{
    public function student(Request $request) {
        $studentList = array_flip(array_flip(array_filter($request->post('student_list', []))));
        if (sizeof($studentList) <= 0) {
            return response(array(
                'message' => 'student_list is empty',
            ), 400);
        }

        $newStudentCount = sizeof($studentList) - Student::whereIn('account', $studentList)->count();

        if ($newStudentCount > 0) {
            $existStudentAccount = Student::whereIn('account', $studentList)->pluck('account');

            $existStudentAccount = json_decode(json_encode($existStudentAccount), true);

            log::debug('', $existStudentAccount);

            if (is_array($existStudentAccount)) {
                $noExistStudentAccount = array_diff($studentList, $existStudentAccount);
            } else {
                $noExistStudentAccount =  $studentList;
            }

            return response()->json(array(
                'count' => $newStudentCount,
                'list' => array_values($noExistStudentAccount),
            ));

        } else {
            return response()->json(['count' => 0]);
        }
    }
}