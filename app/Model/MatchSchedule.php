<?php

namespace App\Model;

use App\Helpers\Helper;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MatchSchedule extends Model
{
    public static $STATUS_COMPLETED = 'completed';
    public static $STATUS_SCHEDULED = 'scheduled';

    public static $SCORING_REJECTED = 'rejected';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    use SoftDeletes;

    protected $table = 'match_schedules';
    protected $dates = ['deleted_at'];
    protected $fillable = array(
        'tournament_id',
        'tournament_group_id',
        'tournament_round_number',
        'tournament_match_number',
        'sports_id',
        'facility_id',
        'facility_name',
        'created_by',
        'match_category',
        'schedule_type',
        'match_type',
        'match_start_date',
        'match_start_time',
        'match_end_date',
        'match_end_time',
        'match_location',
        'longitude',
        'latitude',
        'address',
        'city_id',
        'city',
        'state_id',
        'state',
        'country_id',
        'country',
        'zip',
        'match_status',
        'a_id',
        'b_id',
        'player_a_ids',
        'player_b_ids',
        'score_a',
        'score_b',
        'winner_id',
        'match_details',
        'number_of_rubber',
        'game_type',
        'is_third_position',
        'sports_category',
        'player_or_team_ids'
    );

    public function scheduleteamone()
    {
        return $this->belongsTo('App\Model\Team', 'a_id');
    }

    public function scheduleteamtwo()
    {
        return $this->belongsTo('App\Model\Team', 'b_id');
    }

    public function scheduleuserone()
    {
        return $this->belongsTo('App\User', 'a_id');
    }

    public function scheduleusertwo()
    {
        return $this->belongsTo('App\User', 'b_id');
    }

    public function sport()
    {
        return $this->belongsTo('App\Model\Sport', 'sports_id');
    }

    public function rubbers()
    {
        return $this->hasMany('App\Model\MatchScheduleRubber', 'match_id');
    }

    public function tournament()
    {
        return $this->belongsTo('App\Model\Tournaments', 'tournament_id');
    }

    /**
     * Attributes
     */

    public function getMatchDetailsPAttribute()
    {
        return json_decode($this->match_details);
    }

    public function getMatchDetailsAAttribute()
    {
        return json_decode($this->match_details, true);
    }

    public function getSideAAttribute()
    {
        if ($this->schedule_type == 'team') {
            return $this->scheduleteamone ? $this->scheduleteamone : $this->scheduleteamone()->get();
        } else {
            return $this->scheduleuserone ? $this->scheduleuserone : $this->scheduleuserone()->get();
        }
    }

    public function getSideBAttribute()
    {
        if ($this->schedule_type == 'team') {
            return $this->scheduleteamtwo ? $this->scheduleteamtwo : $this->scheduleteamtwo()->get();
        } else {
            return $this->scheduleusertwo ? $this->scheduleusertwo : $this->scheduleusertwo()->get();
        }
    }

    public function getSideALogoAttribute()
    {
        if ($this->schedule_type == 'team') {
            return Team::logoImage($this->a_id);
        } else {
            return User::logoImage($this->a_id);
        }
    }

    public function getSideBLogoAttribute()
    {
        if ($this->schedule_type == 'team') {
            return Team::logoImage($this->b_id);
        } else {
            return User::logoImage($this->b_id);
        }
    }

    function extractScoreString($id)
    {
        switch ($this->sports_id) {
            case Sport::$BADMINTON:
                return object_get($this->matchDetailsP,'scores.'.$id.'_score'). ' sets';
            case Sport::$VOLEYBALL:
            case Sport::$SQUASH:
            case Sport::$THROW_BALL:
                return object_get($this->matchDetailsP,'scores.'.$id.'_score'). ' sets';
            case Sport::$SOCCER:
            case Sport::$HOKKEY:
                return  object_get($this->matchDetailsP,$id.'.goals');
            case Sport::$BASKETBALL:
            case Sport::$KABADDI:
            case Sport::$ULTIMATE_FRISBEE:
            case Sport::$WATER_POLO :
                return  object_get($this->matchDetailsP,$id.'.total_points');
            case Sport::$CRICKET:
                return  object_get($this->matchDetailsP,$id.'.fst_ing_score') . "/"
                        . object_get($this->matchDetailsP,$id.'.fst_ing_wkt') .
                        (!empty(object_get($this->matchDetailsP,$id.'.scnd_ing_overs')) ?
                            ", " .object_get($this->matchDetailsP,$id.'.scnd_ing_overs') . "/" .
                            object_get($this->matchDetailsP,$id.'.scnd_ing_wkt') : "");
            default:
                return '';
        }
    }

    function extractOversString($id)
    {
        switch ($this->sports_id) {
            case Sport::$BADMINTON:
                return '';
            case Sport::$VOLEYBALL:
            case Sport::$SQUASH:
            case Sport::$THROW_BALL:
              return '';
            case Sport::$SOCCER:
            case Sport::$HOKKEY:
                return '';
            case Sport::$BASKETBALL:
            case Sport::$KABADDI:
            case Sport::$ULTIMATE_FRISBEE:
            case Sport::$WATER_POLO :
                return object_get($this->matchDetailsP,$id.'.total_points');
            case Sport::$CRICKET:
                return object_get($this->matchDetailsP,$id.'.fst_ing_overs').
                         (object_get($this->matchDetailsP,$id.'.scnd_ing_overs') ?
                             '/'.object_get($this->matchDetailsP,$id.'.scnd_ing_overs') :
                             '');
            default:
                return '';
        }

    }




    public function getSideAScoreAttribute()
    {
        return $this->extractScoreString($this->a_id);
    }

    public function getSideBScoreAttribute()
    {
        return $this->extractScoreString($this->b_id);
    }

    public function getSideAOversAttribute(){
        return $this->extractOversString($this->a_id);
    }

    public function getSideBOversAttribute(){
        return $this->extractOversString($this->b_id);
    }


    public function getScoresAttribute()
    {
        if ($this->game_type != 'normal') {
            return $this->a_score . ' - ' . $this->b_score;
        }
        if ($this->match_details != null) {
            $match_details = json_decode($this->match_details);
            $a_id = $this->a_id;
            $b_id = $this->b_id;
            return Helper::getScoresFromMatchDetails($match_details, $this->sports_id, $a_id, $b_id);
        }
        return ' - ';
    }

    public function getWinnerAttribute()
    {
        if (!empty($this->winner_id)) {
            if ($this->schedule_type == 'player') {
                return User::find($this->winner_id)->name;
            } else {
                return Team::find($this->winner_id)->name;
            }
        }
    }

    public function getScoreMoreAttribute()
    {
        if ($this->match_status == 'completed') {
            return trans('message.schedule.viewscore');
        } else {
            if ($this->match_start_date && Carbon::now()->gte(Carbon::createFromFormat('Y-m-d',
                    $this->match_start_date))
            ) {
                $scoreOwner = Helper::isValidUserForScoreEnter($this->toArray());
                if ($scoreOwner) {
                    return Helper::getCurrentScoringStatus($this);
                } else {
                    return trans('message.schedule.viewscore');
                }
            }
        }
    }

   public function referees(){
        return $this->hasMany('App\Model\RefereeSchedule', 'match_id');    
    }

   public function archery_rounds(){
        return $this->hasMany('App\Model\ArcheryRound', 'match_id');
   }

   public function archery_players($team_id=null,$order=null){

    if($this->schedule_type=='player')
        $players = $this->hasMany('App\Model\ArcheryPlayerStats','match_id');
    else
        $players = $this->hasMany('App\Model\ArcheryTeamStats', 'match_id');


        if($team_id) $players = $players->where('team_id',$team_id);

        if($order)   $players = $players->orderBy($order,'desc');
        return $players->get();
   }

    public function match()
    {
        return $this->hasOne('App\Model\SmiteMatch');
    }

    public function smitematchstats()
    {
        return $this->hasOne('App\Model\SmiteMatchStats');
    }


    public function getActiveRubber()
    {
        if (Session::has('rubberInfo')) {
            return Session::get('rubberInfo');
        }

        $active_rubber = MatchScheduleRubber::whereMatchId($this->id)
                                            ->scheduled()
                                            ->orderBy('id', 'asc')
                                            ->first();
        return $active_rubber;
    }


    //function to call sport statistics

    function updateBracketDetails()
    {
        $roundNumber = $this->tournament_round_number;
        $matchNumber = $this->tournament_match_number;
        $matchNumberToCheck = ceil($matchNumber / 2);
        $matchScheduleData = MatchSchedule::where('tournament_id', $this->tournament_id)
                                          ->where('tournament_round_number', $roundNumber + 1)
                                          ->where('tournament_match_number', $matchNumberToCheck)
                                          ->first();
        if ($matchScheduleData) {
            if ($matchScheduleData['schedule_type'] == 'team') {
                $player_b_ids = TeamPlayers::select(DB::raw('GROUP_CONCAT(DISTINCT user_id) AS player_a_ids'))
                                           ->where('team_id', $this->winner_id)->pluck('player_a_ids');
            } else {
                $player_b_ids = $this->winner_id;
            }

            if (!empty($matchScheduleData->a_id)) {
                MatchSchedule::where('id', $matchScheduleData['id'])
                             ->update(['b_id'         => $this->winner_id,
                                       'player_b_ids' => !empty($player_b_ids) ? (',' . trim($player_b_ids) . ',') : NULL]);
            } else {
                MatchSchedule::where('id', $matchScheduleData['id'])
                             ->update(['a_id'         => $this->winner_id,
                                       'player_a_ids' => !empty($player_b_ids) ? (',' . trim($player_b_ids) . ',') : NULL]);
            }

        } else {
            if ($matchScheduleData['schedule_type'] == 'team') {
                $player_a_ids = TeamPlayers::select(DB::raw('GROUP_CONCAT(DISTINCT user_id) AS player_a_ids'))
                                           ->where('team_id',  $this->winner_id)->pluck('player_a_ids');
            } else {
                $player_a_ids = $this->winner_id;
            }
            $scheduleArray = [
                'tournament_id'           => $this->tournament_id,
                'tournament_round_number' => $roundNumber + 1,
                'tournament_match_number' => $matchNumberToCheck,
                'sports_id'               => $this->sports_id,
                'facility_id'             => $this->facility_id,
                'facility_name'           => $this->facility_name,
                'created_by'              => $this->created_by,
                'match_category'          => $this->match_category,
                'schedule_type'           => $this->schedule_type,
                'match_type'              => $this->match_type,
                'match_location'          => $this->match_location,
                'city_id'                 => $this->city_id,
                'city'                    => $this->city,
                'state_id'                => $this->state_id,
                'state'                   => $this->state,
                'country_id'              => $this->country_id,
                'country'                 => $this->country,
                'zip'                     => $this->zip,
                'match_status'            => 'scheduled',
                'a_id'                    => $this->winner_id,
                'game_type'               => $this->game_type,
                'number_of_rubber'        => $this->number_of_rubber,
                'player_a_ids'            => !empty($player_a_ids) ? (',' . trim($player_a_ids) . ',') : NULL,
                'created_at'              => Carbon::now(),
                'updated_at'              => Carbon::now()
            ];

            if (!$this->is_third_position) {
                $matchSchedule = MatchSchedule::create($scheduleArray);
            }

            // Update the winner Id of the for the winner team.
            $maxRoundNumber = MatchSchedule::
            where('tournament_id', $this->tournament_id)->whereNull('tournament_group_id')
                                           ->orderBy('tournament_round_number')
                                           ->max('tournament_round_number');
            $tournamentDetails = Tournaments::where('id', $this->tournament_id)->first(['final_stage_teams']);
            if (count($tournamentDetails)) {
                $lastRoundWinner = intval(ceil(log($tournamentDetails['final_stage_teams'], 2)));
            }
            if (count($maxRoundNumber) && !empty($lastRoundWinner)) {
                if ($maxRoundNumber == $lastRoundWinner + 1) {
                    if (!empty($matchSchedule) && $matchSchedule['id'] > 0) {
                        MatchSchedule::where('id', $matchSchedule['id'])->update([
                            'match_status' => 'completed',
                            'winner_id'    =>  $this->winner_id
                        ]);

                    }
                }
            }
        }


    }

    public function insertPlayerStatistics()
    {
        $match_type = $this->match_type; //!empty($match_data[0]['match_type'])?$match_data[0]['match_type']:'';
        $match_details = $this->match_details;//  !empty($match_data[0]['match_details'])?$match_data[0]['match_details']:'';
        $winner_id = $this->winner_id;// !empty($match_data[0]['winner_id'])?$match_data[0]['winner_id']:'';

        switch ($this->sports_id) {
            case Sport::$TENNIS:
                if ($this->match_details_p)
                    foreach ($this->match_details_p as $key => $players) {
                        $is_win = $winner_id == $key ? 'yes' : 'no';
                        //$this->tennisStatistics($players,$match_type,$is_win);
                    }
                break;
            case Sport::$TABLE_TENNIS:
                if ($this->match_details_p)
                    foreach ($this->match_details_p as $key => $players) {
                        $is_win = $winner_id == $key ? 'yes' : 'no';
                        //$this->tableTennisStatistics($players,$match_type,$is_win);
                    }
                break;
            case Sport::$SOCCER:
                $soccer_details = SoccerPlayerMatchwiseStats::where('match_id', $this->id)->lists(['user_id']);
                foreach ($soccer_details as $user_id) {
                    SoccerStatistic::updateUserStatistic($user_id);
                }
                break;
            case Sport::$HOKKEY:
                $soccer_details = HockeyPlayerMatchwiseStats::where('match_id', $this->id)->lists(['user_id']);
                foreach ($soccer_details as $user_id) {
                    HockeyStatistic::updateUserStatistic($user_id);
                }
                break;
            case Sport::$BASKETBALL:
                $basketball_details = BasketballPlayerMatchwiseStats::where('match_id', $this->id)->lists(['user_id']);
                foreach ($basketball_details as $user_id) {
                    BasketballStatistic::updateUserStatistic($user_id);
                }
                break;
            case Sport::$WATER_POLO:
                // 	$basketball_details = BasketballPlayerMatchwiseStats::where('match_id',$match_id)->get(['user_id']);
                // 	if(!empty($basketball_details) && count($basketball_details)>0)
                // 	{
                // 		foreach($basketball_details as $user_id)
                // 		{
                // 			$this->waterpoloStatistics($user_id['user_id']);
                // 		}

                // 	}
                break;
            case Sport::$KABADDI:
                //$basketball_details = BasketballPlayerMatchwiseStats::where('match_id',$match_id)->get(['user_id']);
                // 	if(!empty($basketball_details) && count($basketball_details)>0)
                // 	{
                // 		foreach($basketball_details as $user_id)
                // 		{
                // 			$this->kabaddiStatistics($user_id['user_id']);
                // 		}

                // 	}
                break;
            case Sport::$VOLEYBALL:
                // {
                // 	$basketball_details = BasketballPlayerMatchwiseStats::where('match_id',$match_id)->get(['user_id']);
                // 	if(!empty($basketball_details) && count($basketball_details)>0)
                // 	{
                // 		foreach($basketball_details as $user_id)
                // 		{
                // 			$this->volleyballStatistics($user_id['user_id']);
                // 		}

                // 	}
                break;
            case Sport::$ULTIMATE_FRISBEE:
                // {
                // 	$basketball_details = BasketballPlayerMatchwiseStats::where('match_id',$match_id)->get(['user_id']);
                // 	if(!empty($basketball_details) && count($basketball_details)>0)
                // 	{
                // 		foreach($basketball_details as $user_id)
                // 		{
                // 			$this->volleyballStatistics($user_id['user_id']);
                // 		}

                // 	}
                break;
            case Sport::$CRICKET:
                $cricket_details = CricketPlayerMatchwiseStats::where('match_id', $this->id)
                                                              ->where('match_type', $match_type)
                                                              ->where('innings', 'first')
                                                              ->get(['user_id']);
                if (!empty($cricket_details) && count($cricket_details) > 0) {
                    foreach ($cricket_details as $players) {
                        $this->cricketBatsmenStatistic($players['user_id'], $match_type, $inning = 'first');//batsmen statistics
                        $this->cricketBowlerStatistic($players['user_id'], $match_type, $inning = 'first');//bowler statistics
                    }

                }

                if ($match_type == 'test')//for test match
                {
                    $cricket_second_ing_details = CricketPlayerMatchwiseStats::where('match_id', $this->id)
                                                                             ->where('match_type', $match_type)
                                                                             ->where('innings', 'second')
                                                                             ->get(['user_id']);
                    if (!empty($cricket_second_ing_details) && count($cricket_second_ing_details) > 0) {
                        foreach ($cricket_second_ing_details as $users) {
                            $this->cricketBatsmenStatistic($users['user_id'], $match_type, $inning = 'second');//batsmen statistics
                            $this->cricketBowlerStatistic($users['user_id'], $match_type, $inning = 'second');//bowler statistics
                        }

                    }

                }
                break;
            default:
                \Log::error('Insert Player Statistics not implemented for sport');
                break;
        };
        $this->updateTournamentGroups();
    }

    private function updateTournamentGroups()
    {
        //if match is scheduled from tournament
        if ($this->tournament_id != '' && $this->tournament_group_id != '') {
            $team_a_id = $this->a_id;
            $team_b_id = $this->b_id;

            $tournamentDetails = $this->tournament;

            $tournament_won_poins = !empty($tournamentDetails[0]['points_win']) ? $tournamentDetails[0]['points_win'] : 0;
            $tournament_lost_poins = !empty($tournamentDetails[0]['points_loose']) ? $tournamentDetails[0]['points_loose'] : 0;
            $tournament_tie_poins = !empty($tournamentDetails[0]['points_tie']) ? $tournamentDetails[0]['points_tie'] : 0;

            $team_a_groupdetails = TournamentGroupTeams::where('tournament_id', $this->tournament_id)
                                                       ->where('tournament_group_id', $this->tournament_group_id)
                                                       ->where('team_id', $team_a_id)->get(['won', 'lost', 'points']);

            $team_b_groupdetails = TournamentGroupTeams::where('tournament_id', $this->tournament_id)
                                                       ->where('tournament_group_id', $this->tournament_group_id)
                                                       ->where('team_id', $team_b_id)->get(['won', 'lost', 'points']);

            $team_a_won_count = !empty($team_a_groupdetails[0]['won']) ? $team_a_groupdetails[0]['won'] : 0;
            $team_a_lost_count = !empty($team_a_groupdetails[0]['lost']) ? $team_a_groupdetails[0]['lost'] : 0;
            $team_a_points = !empty($team_a_groupdetails[0]['points']) ? $team_a_groupdetails[0]['points'] : 0;

            $team_b_won_count = !empty($team_b_groupdetails[0]['won']) ? $team_b_groupdetails[0]['won'] : 0;
            $team_b_lost_count = !empty($team_b_groupdetails[0]['lost']) ? $team_b_groupdetails[0]['lost'] : 0;
            $team_b_points = !empty($team_b_groupdetails[0]['points']) ? $team_b_groupdetails[0]['points'] : 0;

            $tournamentGroupAquery = TournamentGroupTeams::where('tournament_id', $this->tournament_id)
                                                         ->where('tournament_group_id', $this->tournament_group_id)
                                                         ->where('team_id', $team_a_id);

            $tournamentGroupBquery = TournamentGroupTeams::where('tournament_id', $this->tournament_id)
                                                         ->where('tournament_group_id', $this->tournament_group_id)
                                                         ->where('team_id', $team_b_id);
            //if winner id exists
            if ($this->winner_id != '') {
                //if team a wons
                if ($team_a_id == $this->winner_id) {
                    $tournamentGroupAquery->update([
                        'won' => $team_a_won_count + 1, 'points' => $team_a_points + $tournament_won_poins]);

                    $tournamentGroupBquery->update([
                        'lost' => $team_b_lost_count + 1, 'points' => $team_b_points + $tournament_lost_poins]);
                } else {
                    $tournamentGroupAquery->update([
                        'lost' => $team_a_lost_count + 1, 'points' => $team_a_points + $tournament_lost_poins]);

                    $tournamentGroupBquery->update([
                        'won' => $team_b_won_count + 1, 'points' => $team_b_points + $tournament_won_poins
                    ]);
                }
            } else if ($this->is_tied > 0 || $this->match_result == "washout")//if match is tied/washout
            {
                $tournamentGroupAquery
                    ->update(['points' => $team_a_points + $tournament_tie_poins]);

                $tournamentGroupBquery
                    ->update(['points' => $team_b_points + $tournament_tie_poins]);

            }

            //update organization points;

            if (!is_null($this->tournament_id)) {
                Helper::updateOrganizationTeamsPoints($this->tournament_id);
            }

        }
    }
}
