<?php
/**
 * It have many bugs
 * Created in dreaming.
 * User: Boxjan
 * Datetime: 2019-03-16 13:39
 */

namespace App\Http\Controllers;


use App\Log;
use App\Model\Contest;
use App\Model\ContestResult;
use App\Model\Group;
use App\Model\Student;
use App\Services\RankingCalculator\RankCalculator;
use Illuminate\Http\Request;


class StudentController extends Controller
{
    public function index(Request $request) {

        $studentsObj = null;

        if ($request->get('start_at', 0) && $request->get('end_at') &&
            $request->get('start_at') > $request->get('end_at')) {
            abort('400', 'Start cannot be later than the end');
        }

        if ($request->get('name', null) != null) {
            $studentsObj === null ?
                $studentsObj = Student::where('name', 'like', '%' . $request->get('name') . '%') :
                $studentsObj = $studentsObj->where('name', 'like', '%' . $request->get('name') . '%');
        }

        if ($request->get('group', '[]') != '[]') {
            $studentsObj === null ?
                $studentsObj = Student::whereIn('group', json_decode(htmlspecialchars_decode($request->get('group')))) :
                $studentsObj = $studentsObj->whereIn('group', json_decode(htmlspecialchars_decode($request->get('group'))));
        }

        if (\Request::get('type') == 'solved') {
            $ratingsRows = RankCalculator::getSolvedRatingFromIdToId($request->get('start_at', 0),
                $request->get('end_at', 0));
        } else if (\Request::get('type') == 'cf_rating') {
            $ratingsRows = RankCalculator::getCodeforceRatingFromIdToId($request->get('start_at', 0),
                $request->get('end_at', 0));
        } else if (\Request::get('type') == null) {
            $ratingsRows = RankCalculator::getCodeforceRatingFromIdToId($request->get('start_at', 0),
                $request->get('end_at', 0));
        } else {
            return abort(400);
        }

        if (count($ratingsRows) == 0) {
            return abort(400);
        }

        $studentsObj === null ?
            $studentsObj = Student::where('is_show', 0)->select(['student_id', 'name', 'id', 'rating'])->get() :
            $studentsObj = $studentsObj->where('is_show', 0)->select(['student_id', 'name', 'id', 'rating'])->get() ;

        $last = 0;
        $students = [];

        foreach ($studentsObj as $item) {
            $item->rating = 0;
            array_push($students, $item);
        }

        foreach ($ratingsRows as $key => $ratingsRow) {
            $last = $last > $key ? $last : $key;
        }

        foreach ($students as $student) {
            foreach ($ratingsRows as $ratingsRow) {
                if (isset($ratingsRow[$student->id])) {
                    $student->rating = $ratingsRow[$student->id];
                }
            }
        }

        usort($students, array('App\\Http\\Controllers\\StudentController', 'sortByRating'));
        Log::debug('', $students);


        return view('home.student.index', [
            'title' => 'Student List',
            'students' => $students,
            'groups' => Group::select(['id', 'name'])->get(),
            'contests' => Contest::select(['id', 'name'])->get(),
            ]);
    }

    public function show(Request $request, $id) {

        $student = Student::where('is_show', 0)->findOrFail($id);

        if ($request->get('start_at', 0) && $request->get('end_at') &&
            $request->get('start_at') > $request->get('end_at')) {
            abort('400', 'Start cannot be later than the end');
        }

        $ratings = [
            'labels' => [],
            'data' => [],
        ];

        if ($request->get('type') == 'solved') {
            $ratingsRows = RankCalculator::getSolvedRatingFromIdToId($request->get('start_at', 0),
                $request->get('end_at', 0));
        } else if (\Request::get('type') == 'cf_rating') {
            $ratingsRows = RankCalculator::getCodeforceRatingFromIdToId($request->get('start_at', 0),
                $request->get('end_at', 0));
        } else if (\Request::get('type') == null) {
            $ratingsRows = RankCalculator::getCodeforceRatingFromIdToId($request->get('start_at', 0),
                $request->get('end_at', 0));
        } else {
            return abort(404);
        }

        if (count($ratingsRows) == 0) {
            abort(400);
        }

        foreach ($ratingsRows as $contest_id => $row) {
            if (isset($row[$id])) {
                array_push($ratings['labels'], [
                    Contest::where('id', $contest_id)->pluck('name'),
                    Contest::where('id', $contest_id)->pluck('start_time'),
                ]);

                array_push($ratings['data'], $row[$id]);
            }
        }


        Log::debug('', $ratings);

        return view('home.student.show', [
            'title' => 'Student Detail',
            'student' => $student,
            'ratings' => $ratings,
            'contests' => Contest::select(['id', 'name'])->get(),
        ]);
    }

    private function sortByRating($a, $b) {
        return $a->rating < $b->rating;
    }
}