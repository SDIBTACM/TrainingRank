<?php
/**
 * It have many bugs
 * Created in dreaming.
 * User: Boxjan
 * Datetime: 2019-03-17 12:00
 */

namespace App\Services\RankingCalculator;


class StandingsRow
{
    public $party, $rank, $points;

    public function __construct($party, $points) {
        $this->party = $party;
        $this->points = $points;
        $this->rank = 0.0;
    }
}