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
use Illuminate\Validation\Rule;


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
            'contest-name' => 'required|unique:contests,name|max:42',
            'url' => 'required|max:200'
        ]);

        $students = [ ];

        $contest = new Contest;
        $contest->url = $validatedData['url'];
        $contest->name = $validatedData['contest-name'];
        $contest->start_time = $validatedData['contest-time'];
        $contest->save();
        Log::info('User: {}, add new Contest: {}', Auth::user()->username, $contest);
        $contestId = $contest->id;

        $rank_i = 1;
        foreach ($studentsRankRow as $studentRow) {
            if ( ! isset( $studentRow['name'] ) || $studentRow['name'] == null) continue;

            $rank_i ++;

            $studentId = $studentRow['id'];
            if ($studentId == null) {
                $studentId = Student::newStudent($studentRow['name'])->id;
            }

            ContestResult::newResult($studentId, $contest->id,
                $studentRow['rank'] != null ? $studentRow['rank'] : $rank_i, 0,
                $studentRow['solved'] != null ? $studentRow['solved'] : 0);

        }

        $studentRanks = RankCalculator::getCodeforeceRatingByContestId($contestId);

        foreach ($studentRanks as $studentsRank) {
            ContestResult::where('contest_id', $contestId)->where('student_id', $studentsRank->id)
                ->update(['rating' => $studentsRank->newRating]);
        }

        $studentsRanks2 = RankCalculator::getSolvedRatingByContestId($contestId);

        foreach ($studentsRanks2 as $studentsRank) {
            ContestResult::where('contest_id', $contestId)->where('student_id', $studentsRank['id'])
                ->update(['solved_rating' => $studentsRank['rating']]);
        }

        Student::chunk(100, function ($students) {
            foreach ($students as $student) {
                $student->rating = ContestResult::getLatestRatingByStudentId($student->id);
                $student->solved_rating = ContestResult::getSolvedRatingAvgByStudentId($student->id);
                $student->save();
            }
        });

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
                "id" => $row->id,
                'name' => $row->name,
                'student_id' => $row->student_id,
            ];
        }

        foreach ($contestResults as $result) {
            array_push($rank, array(
                'id' => $students[$result->student_id]['id'],
                'name' => $students[$result->student_id]['name'],
                'student_id' => $students[$result->student_id]['student_id'],
                'rank' => $result['rank'],
                'solved' => $result['solved'],
            ) );
        }

        $contest->rank = $rank;


        return view('admin.contest.detail', [
            'contest' => $contest,
            'groups' => Group::select(['id', 'name'])->get(),
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

        Student::chunk(100, function ($students) {
            foreach ($students as $student) {
                $student->rating = ContestResult::getLatestRatingByStudentId($student->id);
                $student->solved_rating = ContestResult::getSolvedRatingAvgByStudentId($student->id);
                $student->save();
            }
        });
    }


    public function update(Request $request, $id) {

        $contest = Contest::find($id);

        $validatedData = $request->validate([
            'contest-time' => 'required|date',
            'contest-name' => [
                'required',
                Rule::unique('contests','name')->ignore($contest->id),
                'max:42'
            ],
            'url' => 'required|max:200'
        ]);

        $contest->url = $validatedData['url'];
        $contest->name = $validatedData['contest-name'];
        $contest->start_time = $validatedData['contest-time'];
        $contest->save();

        ContestResult::where('contest_id', $id)->delete();

        $studentsRankRow = json_decode($request->post('contest-rank', ''), true);
        $rank_i = 1;
        foreach ($studentsRankRow as $studentRow) {
            if ( ! isset( $studentRow['name'] ) || $studentRow['name'] == null) continue;

            $rank_i ++;

            $studentId = $studentRow['id'];
            if ($studentId == null) {
                $studentId = Student::newStudent($studentRow['name'])->id;
            }

            ContestResult::newResult($studentId, $contest->id,
                $studentRow['rank'] != null ? $studentRow['rank'] : $rank_i, 0,
                $studentRow['solved'] != null ? $studentRow['solved'] : 0);

        }

        $contestMaxId = Contest::orderBy('id', 'desc')->value('id');

        for ($contestId = $contest->id; $contestId <= $contestMaxId; $contestId++) {
            if (Contest::where('id', $contestId)->count() == 0) continue;

            $studentRanks = RankCalculator::getCodeforeceRatingByContestId($contestId);

            foreach ($studentRanks as $studentsRank) {
                ContestResult::where('contest_id', $contestId)->where('student_id', $studentsRank->id)
                    ->update(['rating' => $studentsRank->newRating]);
            }

            $studentsRanks2 = RankCalculator::getSolvedRatingByContestId($contestId);

            foreach ($studentsRanks2 as $studentsRank) {
                ContestResult::where('contest_id', $contestId)->where('student_id', $studentsRank['id'])
                    ->update(['solved_rating' => $studentsRank['rating']]);
            }

        }

        Student::chunk(100, function ($students) {
            foreach ($students as $student) {
                $student->rating = ContestResult::getLatestRatingByStudentId($student->id);
                $student->solved_rating = ContestResult::getSolvedRatingAvgByStudentId($student->id);
                $student->save();
            }
        });


        return redirect()->route('admin.contest.index');
    }
}