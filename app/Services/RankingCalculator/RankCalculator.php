<?php
/**
 * It have many bugs
 * Created in dreaming.
 * User: Boxjan
 * Datetime: 2019-03-17 11:43
 */

namespace App\Services\RankingCalculator;


use App\Log;
use App\Model\Contest;
use App\Model\ContestResult;
use Illuminate\Support\Facades\Cache;

class RankCalculator
{
    static public function getCodeforceRatingFromIdToId($start_at = 0, $end_at = 0) {

        Log::debug('', $start_at, $end_at);

        if (Contest::count() == 0) {
            return [];
        }

        if ($end_at == 0) $end_at = ($start_at == 0) ? Contest::orderBy('id', 'desc')->first()->id : $start_at;
        if ($start_at == 0) $start_at = Contest::first()->id;


        Log::debug('', $start_at, $end_at);

        if (Cache::has("contest:$start_at:$end_at:cf_rating") && config('app.debug') == false) {
            return json_decode(Cache::get("contest:$start_at:$end_at:cf_rating"), true);
        }

        $result = [];
        $lastCalcContestId = $start_at;

        for($i = $start_at; $i <= $end_at; $i++) {
            if (Contest::where('id', $i)->count() == 0) {
                continue;
            }

            Log::info('start calc contest:', $i);
            $studentRows = ContestResult::where('contest_id', $i)->get();

            $students = [];

            foreach ($studentRows as $studentRow) {
                if ($start_at == $i || isset($result[$lastCalcContestId][$studentRow->student_id]) == false) {
                    $lastRating = 1500;
                } else {
                    $lastRating = $result[$lastCalcContestId][$studentRow->student_id];
                }

                array_push($students, new Student($studentRow->student_id,
                    $studentRow->rank, $lastRating));
            }

            self::getCodeforeceRating($students);

            $studentsRes = [];

            foreach ($students as $student) {
                $studentsRes[$student->id] = $student->newRating;
            }

            $result[$i] = $studentsRes;
            $lastCalcContestId = $i;

        }
        Log::debug('', $result);

        Cache::set("contest:$start_at:$end_at:cf_rating", json_encode($result));

        return $result;
    }

    static public function getCodeforeceRatingByContestId($contest_id) {

        $students = [];
        $studentRows = ContestResult::where('contest_id', $contest_id)->get();

        Log::info('start calc contest:', $contest_id);

        foreach ($studentRows as $studentRow) {
            $contestResult =  ContestResult::where('contest_id', '<', $contest_id)
                ->where('student_id', $studentRow->student_id)
                ->orderBy('contest_id', 'desc')->first();

            array_push($students, new Student($studentRow->student_id,
                $studentRow->rank, $contestResult->rating));
        }

        self::getCodeforeceRating($students);
        return $students;

    }

    static public function getCodeforeceRating(Array &$rows) {
        $previousRatings = [];
        $standingsRows = [];
        $ratingChanges = [];

        foreach ($rows as $row) {
            $previousRatings[$row->id] = $row->previousRating;
        }

        foreach ($rows as $row) {
            array_push($standingsRows, new StandingsRow($row->id, sizeof($rows) - $row->position));
        }

        $calc = new CodeforeceRatingCalculator();
        $calc->calculateRatingChanges($previousRatings, $standingsRows, $ratingChanges);

        foreach ($rows as $row) {
            $row->delta = $ratingChanges[$row->id];
            $row->newRating = $row->previousRating + $row->delta;
        }
    }

    static public function getSolvedRatingFromIdToId($start_at = 0, $end_at = 0) {

        if (Contest::count() == 0) {
            return [];
        }

        if ($end_at == 0) $end_at = ($start_at == 0) ? Contest::orderBy('id', 'desc')->first()->id : $start_at;
        if ($start_at == 0) $start_at = Contest::first()->id;

        Log::debug('', $start_at, $end_at);

        if (Cache::has("contest:$start_at:$end_at:rank_rating") && config('app.debug') == false) {
            return json_decode(Cache::get("contest:$start_at:$end_at:cf"), true);
        }

        $result = [];

        for($i = $start_at; $i <= $end_at; $i++) {
            if (Contest::where('id', $i)->count() == 0) {
                continue;
            }

            Log::info('start calc contest:', $i);
            $studentRows = ContestResult::where('contest_id', $i)->get();

            $students = [];

            foreach ($studentRows as $studentRow) {
                array_push($students, [

                    'id' => $studentRow['student_id'],
                    'solved' => $studentRow['solved'],
                    'rank' => $studentRow['rank']

                ]);
            }

            self::getSolvedRating($students);

            $studentsRes = [];

            foreach ($students as $student) {
                $studentsRes[$student['id']] = $student['rating'];
            }

            $result[$i] = $studentsRes;
        }

        Cache::set("contest:$start_at:$end_at:rank_rating", json_encode($result));
        Log::debug('', $result);
        return $result;
    }


    static public function getSolvedRatingByContestId($contest_id) {
        $students = [];
        $studentRows = ContestResult::where('contest_id', $contest_id)->get();

        Log::info('start calc contest:', $contest_id);

        foreach ($studentRows as $studentRow) {
            array_push($students, [

                'id' => $studentRow['student_id'],
                'solved' => $studentRow['solved'],
                'rank' => $studentRow['rank']

            ]);
        }
        self::getSolvedRating($students);
        return $students;
    }

    static public function getSolvedRating(Array &$rows) {
        $calc = new SolvedRatingCalculate();
        $calc->calculateRating($rows);
    }

}