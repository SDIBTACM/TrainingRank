<?php
/**
 * It have many bugs
 * Created in dreaming.
 * User: Boxjan
 * Datetime: 2019-03-16 13:28
 */

namespace App\Services\RankingCalculator;


class Contestant
{
    public $party, $rank, $points, $rating, $seed, $delta, $needRating;

    public function __construct($party, $rank, $points, $rating) {
        $this->party = $party;
        $this->rank = $rank;
        $this->points = $points;
        $this->rating = $rating;
    }
}