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

class RankCalculator
{
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
}