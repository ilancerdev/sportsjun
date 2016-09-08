<?php
namespace App\Helpers;
use App\Model\Photo;
use App\Model\Team;
use App\Model\Sport;
use App\Model\UserStatistic;
use App\Model\Notifications;
use App\Model\MatchSchedule;
use App\Model\MatchScheduleRubber;
use App\Model\TournamentParent;
use App\Model\Tournaments;
use App\Model\TeamPlayers;
use App\Model\volleyballScore;
use App\Model\VolleyballPlayerMatchwiseStats;
use App\Model\TournamentMatchPreference;
use App\Helpers\AllRequests;
use App\User;
use App\Model\Organization;
use File;
use Auth;
use DB;
use View;
use Carbon\Carbon;
use Route;
use PDO;
use App\Helpers\SendMail;
use App\Model\TournamentGroupTeams;
use App\Model\OrganizationGroupTeamPoint;

use App\Model\BadmintonPlayerMatchwiseStats;
use App\Model\BadmintonPlayerMatchScore;
use App\Model\BadmintonPlayerRubberScore;
use App\Model\BadmintonStatistic;

class ScoreCard {

		//get rubber player statictics
 	public static function	getRubberPlayers($rubber_id, $sports_id=5){
 		$score_a_array=[];
 		$score_b_array=[];

 		switch ($sports_id) {
 			case 5:		

 		$scores_a = BadmintonPlayerRubberScore::select()->where('rubber_id',$rubber_id)->first();
        $scores_b = BadmintonPlayerRubberScore::select()->where('rubber_id',$rubber_id)->skip(1)->first();             
            if(count($scores_a)>0)
                $score_a_array = $scores_a->toArray();

            if(count($scores_b)>0)
                $score_b_array = $scores_b->toArray();

        return [
        	'a'=>$score_a_array,
        	'b'=>$score_b_array
        ];

        # code...
 				break;


        default:
 				# code...
 				break;
 		}
	}

//get the winner in rubbers;
	public static function getWinnerInRubber($match_id, $sports_id=5){
			$match_model=MatchSchedule::find($match_id);
			$a_id=$match_model->a_id;
			$b_id=$match_model->b_id;

			$score_a=0;
			$score_b=0;

			switch ($sports_id) {
				case '5':
			$score_a=MatchScheduleRubber::where('winner_id', $a_id)->whereMatchId($match_id)->get()->count();
			$score_b=MatchScheduleRubber::where('winner_id', $b_id)->whereMatchId($match_id)->get()->count();
					break;
				
				default:
					# code...
					break;
			}
		$match_model->a_score=$score_a;
		$match_model->b_score=$score_b;
		$match_model->save();

		if($score_a>$score_b) return ['winner'=>$a_id, 'looser'=> $b_id];
		else return ['winner'=>$b_d, 'looser'=> $a_id];

	}

}

?>