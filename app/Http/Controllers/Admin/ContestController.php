<?php
/**
 * It have many bugs
 * Created in dreaming.
 * User: Boxjan
 * Datetime: 2019-03-16 13:39
 */

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Log;
use App\Model\Contest;
use App\Model\ContestResult;
use App\Model\Group;
use App\Model\Student;
use App\Services\RankingCalculator\RankCalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class ContestController extends Controller
{
    public function create() {
        return view("admin.contest.create", [
            'groups' => Group::select(['id', 'name'])->get(),
        ]);
    }

    public function store(Request $request) {

        $studentsRankRow = json_decode($request->post('contest-rank', ''), true);

        if (sizeof($studentsRankRow) == 0) {
            return response(["message" =>  "Student Rank is Empty"], 400);
        }

        $validatedData = $request->validate([
            'contest-time' => 'required|date',
            'contest-name' => 'required|unique:contests,name|max:42'
        ]);

        $students = [ ];

        $contest = new Contest;
        $contest->name = $validatedData['contest-name'];
        $contest->start_time = $validatedData['contest-time'];
        $contest->save();
        Log::info('User: {}, add new Contest: {}', Auth::user()->username, $contest);
        $contestId = $contest->id;

        $rank_i = 1;
        foreach ($studentsRankRow as $studentRow) {
            $rank_i ++;

            if ($studentRow['name'] == null && $studentRow['id'] == null) continue;

            $studentId = $studentRow['id'];
            if ($studentId == null) {
                $studentId = Student::newStudent($studentRow['name'])->id;
            }

            array_push($students, new \App\Services\RankingCalculator\Student($studentId,
                $studentRow['rank'] != null ? $studentRow['rank'] : $rank_i,
                ContestResult::getLatestRatingByStudentId($studentId)));
        }

        RankCalculator::getCodeforeceRating($students);

        foreach ($students as $student) {
            ContestResult::newResult($student->id, $contestId, $student->position, $student->newRating);
        }

        return redirect()->route('admin.contest.index');

    }

    public function index() {
        return view("admin.contest.index", [
            'title' => 'Contest Manger',
            'contests' => Contest::paginate(15),
        ]);
    }

    public function show($id) {

        $contest = Contest::findOrFail($id);

        $contestResults = ContestResult::where('contest_id', $id)->orderBy('rank', 'asc')->get();
        $studentsRow = Student::whereIn('id', ContestResult::where('contest_id', $id)->pluck('student_id') )->get();


        $rank = [];
        $students = [];
        foreach ($studentsRow as $row) {
            $students[$row->id] = [
                'name' => $row->name,
                'account' => $row->account,
            ];
        }

        foreach ($contestResults as $result) {
            array_push($rank, array(
                'student_name' => $students[$result->student_id]['name'],
                'student_account' => $students[$result->student_id]['account'],
                'student_rank' => $result['rank'],
            ) );
        }

        $contest->rank = $rank;


        return view('admin.contest.detail', [
            'contest' => $contest,
        ]);
    }

    public function destroy($id)
    {
        if ( $id != Contest::orderBy('id', 'desc')->value('id') ) {
            return response('you should delete the latest Contest', 400);
        }

        $oldContest = Contest::findOrFail($id);

        ContestResult::where('contest_id', $id)->delete();

        Contest::destroy($id);
        Log::info('User: {} delete contest: {}', Auth::user()->username, $oldContest);
    }
}