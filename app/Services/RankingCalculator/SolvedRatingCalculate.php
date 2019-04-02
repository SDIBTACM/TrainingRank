<?php
/**
 * It have many bugs
 * Created in dreaming.
 * User: Boxjan
 * Datetime: 2019-04-02 19:51
 */

namespace App\Services\RankingCalculator;


use App\Log;

class SolvedRatingCalculate
{
    public function calculateRating(&$stangingRows) {

        $maxED = 0;
        $count = count($stangingRows);

        foreach ($stangingRows as &$row) {
            $row['calc_mid'] = $row['solved'] * 2 + $count + 1 - $row['rank'];
            $maxED < $row['calc_mid'] ? $maxED = $row['calc_mid'] : null;
        }
        if ($maxED <= 0) {
            Log::warning("Solved Rating Calc Err: value max error, value: {}", $maxED);
            return;
        }

        foreach ($stangingRows as &$row) {
            $row['rating'] = $row['calc_mid'] * 100 / $maxED;
        }

    }
}