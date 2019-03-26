<?php
/**
 * It have many bugs
 * Created in dreaming.
 * User: Boxjan
 * Datetime: 2019-03-16 13:18
 */

namespace App\Services\RankingCalculator;

use App\Log;

class CodeforeceRatingCalculator
{
    public function calculateRatingChanges(&$previousRatings, &$stangingRows, &$ratingChanges) {
        $contestants = array();

        foreach ($stangingRows as $stangingRow) {
            array_push($contestants, new Contestant($stangingRow->party, $stangingRow->rank, $stangingRow->points, $previousRatings[$stangingRow->party]));
        }

        $this->process($contestants);

        foreach ($contestants as $contestant) {
            $ratingChanges[$contestant->party] = $contestant->delta;
        }

    }

    private function process(&$contestants) {
        if (sizeof($contestants) == 0) {
            return null;
        }

        $this->reassignRanks($contestants);


        foreach ($contestants as $contestant_a) {
            $contestant_a->seed = 1;

            foreach ($contestants as $contestant_b) {
                if ($contestant_a !== $contestant_b) {
                    $contestant_a->seed += $this->getEloWinProbability($contestant_b, $contestant_a);
                }
            }

        }

        foreach ($contestants as $contestant) {
            $midRank = sqrt($contestant->rank * $contestant->seed);
            $contestant->needRating = $this->getRatingToRank($contestants, $midRank);
            $contestant->delta = floor( ($contestant->needRating - $contestant->rating) / 2 );
        }

        $this->sortByRatingDesc($contestants);

        // Total sum should not be more than zero.
        {
            $sum = 0;

            foreach ($contestants as $contestant) {
                $sum += $contestant->delta;
            }

            $inc = intval( - $sum / sizeof($contestants) - 1 );

            foreach ($contestants as $contestant) {
                $contestant->delta += $inc;
            }
        }

        // Sum of top-4*sqrt should be adjusted to zero.
        {
            $sum = 0;
            $zeroSumCount = min( floor(4 * round( sqrt( sizeof($contestants) ) ) ) , sizeof( $contestants ) );

            for ($i = 0; $i < $zeroSumCount; $i++) {
                $sum += $contestants[$i]->delta;
            }

            $inc = min( max( floor( -$sum / $zeroSumCount) , -10 ), 0 );

            foreach ($contestants as $contestant) {
                $contestant->delta += $inc;
            }
        }

        try {
            $this->validateDeltas($contestants);
        } catch (\Exception $e) {
            Log::error("", $e->getMessage());
            return false;
        }

        return true;

    }

    private function reassignRanks(&$contestants) {
        $this->sortByPointsDesc($contestants);

        foreach ($contestants as $contestant) {
            $contestant->rank = 0;
            $contestant->delta = 0;
        }

        $first = 0;
        $points = $contestants[0]->points;

        for ($i = 1; $i < sizeof($contestants); $i++) {

            if ($contestants[$i]->points < $points) {

                for ($j = $first; $j < $i; $j++) {
                    $contestants[$j]->rank = $i;
                }

                $first = $i;
                $points = $contestants[$i]->points;

            }
        }

        {
            $rank = sizeof($contestants);
            for ($i = $first; $i < sizeof($contestants); $i++) {
                $contestants[$i]->rank = $rank;
            }
        }
    }

    private function getRatingToRank(&$contestants, &$rank) {
        $left = 1;
        $right = 8000;

        while ($right - $left > 1) {
            $mid = floor( ($left + $right) / 2 ) ;

            if ( $this->getSeed($contestants, $mid) < $rank) {
                $right = $mid;
            } else {
                $left = $mid;
            }
        }

        return $left;
    }

    private function getSeed(&$contestants, &$rating) {
        $extraContestant = new Contestant(null, 0, 0, $rating);

        $result = 1;

        foreach ($contestants as $contestant) {
            $result += $this->getEloWinProbability($contestant, $extraContestant);
        }
        return $result;
    }

    /**
     * @param $contestants
     * @throws \Exception
     */
    private function validateDeltas(&$contestants) {
        $this->sortByPointsDesc($contestants);

        for ($i = 0; $i < sizeof($contestants); $i++) {
            for ($j = $i + 1; $j < sizeof($contestants); $j++) {

                if ($contestants[$i]->rating > $contestants[$j]->rating) {
                    // If a contestant a also has higher rating than j
                    // So, a's rating should stay higher than j's.
                    if ($contestants[$i]->rating + $contestants[$i]->delta < $contestants[$j]->rating + $contestants[$j]->delta) {
                        throw new \Exception('First rating invariant failed ' .
                            $contestants[$i]->party . ': {'. ($contestants[$i]->rating + $contestants[$i]->delta) .' }' . ' vs. ' .
                            $contestants[$j]->party . ': {'. ($contestants[$j]->rating + $contestants[$j]->delta) .' }' . '.');
                    }
                }


                if ($contestants[$i]->rating > $contestants[$j]->rating) {
                    // If a contestant a also has higher rating than j
                    // So, a's rating should stay higher than j's.
                    if ($contestants[$i]->delta < $contestants[$j]->delta) {
                        throw new \Exception('Second rating invariant failed ' .
                            $contestants[$i]->party . ": {$contestants[$i]->delta}". ' vs. ' .
                        $contestants[$j]->party . ": {$contestants[$j]->delta}" . '.');
			        }
                }

            }
        }
    }


    private function sortByPointsDesc(Array &$contestants) {
        usort($contestants, array(__CLASS__, "cmpByPoints"));
    }

    private function sortByRatingDesc(Array &$contestants) {
        usort($contestants, array(__CLASS__, "cmpByRating"));
    }

    private function getEloWinProbabilityByRating($ra, $rb) {
        return 1.0 / (1 + pow(10, ($rb - $ra) / 400.0));
    }

    private function getEloWinProbability(Contestant $a, Contestant $b) {
        return $this->getEloWinProbabilityByRating($a->rating, $b->rating);
    }

    private function cmpByPoints(Contestant $a, Contestant $b) {
        return $a->points < $b->points;
    }

    private function cmpByRating (Contestant $a, Contestant $b) {
        return $a->rating < $b->rating;
    }

};


