<?php
/**
 * It have many bugs
 * Created in dreaming.
 * User: Boxjan
 * Datetime: 2019-03-17 11:43
 */

namespace App\Services\RankingCalculator;


class RankCalculator
{
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