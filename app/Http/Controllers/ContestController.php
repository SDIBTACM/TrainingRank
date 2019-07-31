<?php
/**
 * It have many bugs
 * Created in dreaming.
 * User: Boxjan
 * Datetime: 2019-03-20 20:04
 */

namespace App\Http\Controllers;

use App\Model\Contest;
use App\Model\ContestResult;
use Illuminate\Http\Request;

class ContestController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request) {

        $contests = Contest::OrderBy('start_time', 'desc')->paginate(20);

        return view('home.contest.index', [
            'title' => 'Contest List',
            'contests' => $contests,
        ]);
    }

    public function show($id) {
        $contest = Contest::findOrFail($id);

        $contestResults = ContestResult::with(['student' => function ($query) {
            $query->where('is_show', 0);
        }])->where('contest_id', $id)->OrderBy('rank')->get();

        $ranks = [];
        foreach ($contestResults as $result) {
            array_push($ranks, [
                'rank' => $result->rank,
                'student' => [
                    'name' => $result->student['name'],
                    'student_id' => $result->student['student_id'],
                ],
            ]);
        }

        return view('home.contest.show', [
            'title' => 'Contest Detail',
            'contest' => $contest,
            'ranks' => $ranks,
        ]);
    }
}
