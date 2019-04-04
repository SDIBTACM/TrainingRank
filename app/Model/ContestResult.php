<?php
/**
 * It have many bugs
 * Created in dreaming.
 * User: Boxjan
 * Datetime: 2019-03-16 14:56
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class ContestResult extends Model
{


    static public function newStudent($student_id) {
        $row = new self;
        $row->contest_id = 0;
        $row->student_id = $student_id;
        $row->rank = 1;
        $row->rating = 1500;
        $row->solved = 0;

        $row->save();
    }

    static public function newResult($student_id, $contest_id, $rank, $rating, $solved) {
        $row = new self;
        $row->contest_id = $contest_id;
        $row->student_id = $student_id;
        $row->rank = $rank;
        $row->rating = $rating;
        $row->solved = $solved;

        $row->save();
    }

    static public function getLatestRatingByStudentId($studentId) {
        return self::where('student_id', $studentId)->orderBy('contest_id', 'desc')->value('rating');
    }

    static public function getRatingByStudentIdAndContestId($studentId, $contest_id) {
        return self::where('student_id', $studentId)->where('contest_id', $contest_id)->value('rating');
    }

    static public function getRankByStudentIdAndContestId($studentId, $contest_id) {
        return self::where('student_id', $studentId)->where('contest_id', $contest_id)->value('rank');
    }

    static public function getSolvedRatingAvgByStudentId($studentId) {
        return self::where('student_id', $studentId)->where('solved_rating', '>', '0')->avg('solved_rating');
    }

    public function student() {
        return $this->belongsTo('App\Model\Student');
    }

    public function contest() {
        return $this->belongsTo('App\Model\Contest');
    }
}