<?php
/**
 * It have many bugs
 * Created in dreaming.
 * User: Boxjan
 * Datetime: 2019-03-17 11:41
 */

namespace App\Services\RankingCalculator;


class Student
{
    public $id, $position, $previousRating, $delta, $newRating, $rank;

    public function __construct($id, $position, $previousRating) {
        $this->id = $id;
        $this->position = $position;
        $this->previousRating = $previousRating;
        $this->delta = 0;
        $this->newRating = 0;
    }
}