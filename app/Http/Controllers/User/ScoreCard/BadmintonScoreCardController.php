<?php

namespace App\Http\Controllers\User\ScoreCard;

use App\Helpers\Helper;
use App\Helpers\ScoreCard;
use App\Http\Controllers\User\ScheduleController;
use App\Http\Controllers\User\ScoreCardController as parentScoreCardController;
use App\Model\BadmintonPlayerMatchScore;
use App\Model\BadmintonPlayerRubberScore;
use App\Model\BadmintonStatistic;
use App\Model\MatchSchedule;
use App\Model\MatchScheduleRubber;
use App\Model\Photo;
use App\Model\Sport;
use App\Model\Team;
use App\Model\Tournaments;
use App\User;
use Auth;
use Illuminate\Http\Request as ObjectRequest;
use Request;
use Session;       //get all my requests data as object

class BadmintonScoreCardController extends parentScoreCardController
{


    public function badmintonScoreCard($match_data, $match, $sportsDetails = [], $tournamentDetails = [], $is_from_view = 0)
    {

        $game_type = $match_data[0]['game_type'];
        if ($game_type == 'rubber') {
            $rubbers = MatchscheduleRubber::whereMatchId($match_data[0]['id'])->get();

            if (!count($rubbers)) {
                $scheduleController = new ScheduleController;
                $rubbers = $scheduleController->insertGroupRubber($match_data[0]['id']);
            }

            $active_rubber = $this->getActiveRubber($match_data[0]['id']);


            if (count($active_rubber)) {

                $rubber_details = $active_rubber;
                $active_rubber = $active_rubber->rubber_number;
            } else {
                $active_rubber = null;
                $rubber_details = null;
            }

        } else {
            $active_rubber = null;
            $rubber_details = null;
            $rubbers = [];
        }


        $score_a_array = [];
        $score_b_array = [];

        $loginUserId = '';
        $loginUserRole = '';

        if (isset(Auth::user()->id))
            $loginUserId = Auth::user()->id;

        if (isset(Auth::user()->role))
            $loginUserRole = Auth::user()->role;

        //!empty($matchScheduleDetails['tournament_id'])
        //if($match_data[0]['match_status']=='scheduled')//match should be already scheduled
        //{
        $player_a_ids = $match_data[0]['player_a_ids'];
        $player_b_ids = $match_data[0]['player_b_ids'];

        $decoded_match_details = [];
        if ($match_data[0]['match_details'] != '') {
            $decoded_match_details = json_decode($match_data[0]['match_details'], true);
        }

        $a_players = [];

        $team_a_playerids = (!empty($decoded_match_details[$match_data[0]['a_id']]) && !($match_data[0]['scoring_status'] == '' || $match_data[0]['scoring_status'] == 'rejected')) ? $decoded_match_details[$match_data[0]['a_id']] : explode(',', $player_a_ids);

        $a_team_players = User::select('id', 'name')->whereIn('id', $team_a_playerids)->get();

        if (count($a_team_players) > 0)
            $a_players = $a_team_players->toArray();

        $b_players = [];

        $team_b_playerids = (!empty($decoded_match_details[$match_data[0]['b_id']]) && !($match_data[0]['scoring_status'] == '' || $match_data[0]['scoring_status'] == 'rejected')) ? $decoded_match_details[$match_data[0]['b_id']] : explode(',', $player_b_ids);


        $b_team_players = User::select('id', 'name')->whereIn('id', $team_b_playerids)->get();

        if (count($b_team_players) > 0)
            $b_players = $b_team_players->toArray();

        $team_a_player_images = [];
        $team_b_player_images = [];

        //team a player images
        if (count($a_players) > 0) {
            foreach ($a_players as $a_player) {
                $team_a_player_images[$a_player['id']] = Photo::select()->where('imageable_id', $a_player['id'])
                                                              ->where('imageable_type', config('constants.PHOTO.USER_PHOTO'))
                                                              ->orderBy('id', 'desc')->first();//get user logo
            }
        }

        //team b player images
        if (count($b_players) > 0) {
            foreach ($b_players as $b_player) {
                $team_b_player_images[$b_player['id']] = Photo::select()->where('imageable_id', $b_player['id'])
                                                              ->where('imageable_type', config('constants.PHOTO.USER_PHOTO'))
                                                              ->orderBy('id', 'desc')->first();//get user logo
            }
        }
        if ($match_data[0]['schedule_type'] == 'player')//&& $match_data[0]['schedule_type'] == 'player'
        {
            $user_a_name = User::where('id', $match_data[0]['a_id'])->pluck('name');
            $user_b_name = User::where('id', $match_data[0]['b_id'])->pluck('name');
            $user_a_logo = Photo::select()->where('imageable_id', $match_data[0]['a_id'])
                                ->where('imageable_type', config('constants.PHOTO.USER_PHOTO'))->orderBy('id', 'desc')
                                ->first();//get user logo
            $user_b_logo = Photo::select()->where('imageable_id', $match_data[0]['b_id'])
                                ->where('imageable_type', config('constants.PHOTO.USER_PHOTO'))->orderBy('id', 'desc')
                                ->first();//get user logo
            $upload_folder = 'user_profile';
            $is_singles = 'yes';

            if ($game_type == 'normal') {
                $scores_a = BadmintonPlayerMatchScore::select()->where('match_id', $match_data[0]['id'])->first();
                $scores_b = BadmintonPlayerMatchScore::select()->where('match_id', $match_data[0]['id'])->skip(1)
                                                     ->first();
            } else {
                // $scores_a = BadmintonPlayerRubberScore::select()->where('match_id',$match_data[0]['id'])->first();
                // $scores_b = BadmintonPlayerRubberScore::select()->where('match_id',$match_data[0]['id'])->skip(1)->first();
            }


            if (count($scores_a) > 0)
                $score_a_array = $scores_a->toArray();

            if (count($scores_b) > 0)
                $score_b_array = $scores_b->toArray();

            $team_a_city = Helper::getUserCity($match_data[0]['a_id']);
            $team_b_city = Helper::getUserCity($match_data[0]['b_id']);
        } else {
            $user_a_name = Team::where('id', $match_data[0]['a_id'])->pluck('name');//team details
            $user_b_name = Team::where('id', $match_data[0]['b_id'])->pluck('name');//team details
            $user_a_logo = Photo::select()->where('imageable_id', $match_data[0]['a_id'])
                                ->where('imageable_type', config('constants.PHOTO.TEAM_PHOTO'))->orderBy('id', 'desc')
                                ->first();//get user logo
            $user_b_logo = Photo::select()->where('imageable_id', $match_data[0]['b_id'])
                                ->where('imageable_type', config('constants.PHOTO.TEAM_PHOTO'))->orderBy('id', 'desc')
                                ->first();//get user logo
            $upload_folder = 'teams';
            $is_singles = 'no';

            if ($game_type == 'normal') {
                $scores_a = BadmintonPlayerMatchScore::select()->where('match_id', $match_data[0]['id'])->first();
                $scores_b = BadmintonPlayerMatchScore::select()->where('match_id', $match_data[0]['id'])->skip(1)
                                                     ->first();
            } else {
                $scores_a = BadmintonPlayerRubberScore::select()->where('match_id', $match_data[0]['id'])->first();
                $scores_b = BadmintonPlayerRubberScore::select()->where('match_id', $match_data[0]['id'])->skip(1)
                                                      ->first();
            }

            if (count($scores_a) > 0)
                $score_a_array = $scores_a->toArray();

            if (count($scores_b) > 0)
                $score_b_array = $scores_b->toArray();

            $team_a_city = Helper::getTeamCity($match_data[0]['a_id']);
            $team_b_city = Helper::getTeamCity($match_data[0]['b_id']);
        }

        //bye match
        if ($match_data[0]['b_id'] == '' && $match_data[0]['match_status'] == 'completed') {
            $sport_class = 'badminton_scorcard';
            return view('scorecards.byematchview', [
                'team_a_name'   => $user_a_name, 'team_a_logo' => $user_a_logo, 'match_data' => $match_data,
                'upload_folder' => $upload_folder, 'sport_class' => $sport_class]);
        }


        //score status
        $score_status_array = json_decode($match_data[0]['score_added_by'], true);


        $rej_note_str = '';
        if ($score_status_array['rejected_note'] != '') {
            $rejected_note_array = explode('@', $score_status_array['rejected_note']);
            $rejected_note_array = array_filter($rejected_note_array);
            foreach ($rejected_note_array as $note) {
                $rej_note_str = $rej_note_str . $note . ' ,';
            }
        }
        $rej_note_str = trim($rej_note_str, ",");


        //is valid user for score card enter or edit
        $isValidUser = 0;
        $isApproveRejectExist = 0;
        $isForApprovalExist = 0;
        if (isset(Auth::user()->id)) {
            $isValidUser = Helper::isValidUserForScoreEnter($match_data);
            //is approval process exist
            $isApproveRejectExist = Helper::isApprovalExist($match_data);
            $isForApprovalExist = Helper::isApprovalExist($match_data, $isForApproval = 'yes');
        }

        $isAdminEdit = 0;
        if (Session::has('is_allowed_to_edit_match')) {
            $session_data = Session::get('is_allowed_to_edit_match');

            if ($isValidUser && ($session_data[0]['id'] == $match_data[0]['id'])) {
                $isAdminEdit = 1;
            }
        }


        //ONLY FOR VIEW SCORE CARD
        if (($is_from_view == 1 || (!empty($score_status_array['added_by']) && $score_status_array['added_by'] != $loginUserId && $match_data[0]['scoring_status'] != 'rejected') || $match_data[0]['match_status'] == 'completed' || $match_data[0]['scoring_status'] == 'approval_pending' || $match_data[0]['scoring_status'] == 'approved' || !$isValidUser) && !$isAdminEdit) {

            return view('scorecards.badminton.badmintonscorecardview', [
                'tournamentDetails'     => $tournamentDetails, 'sportsDetails' => $sportsDetails,
                'user_a_name'           => $user_a_name, 'user_b_name' => $user_b_name, 'user_a_logo' => $user_a_logo,
                'user_b_logo'           => $user_b_logo, 'match_data' => $match_data, 'upload_folder' => $upload_folder,
                'is_singles'            => $is_singles, 'score_a_array' => $score_a_array,
                'score_b_array'         => $score_b_array, 'b_players' => $b_players, 'a_players' => $a_players,
                'team_a_player_images'  => $team_a_player_images, 'team_b_player_images' => $team_b_player_images,
                'decoded_match_details' => $decoded_match_details, 'score_status_array' => $score_status_array,
                'loginUserId'           => $loginUserId, 'rej_note_str' => $rej_note_str,
                'loginUserRole'         => $loginUserRole, 'isValidUser' => $isValidUser,
                'isApproveRejectExist'  => $isApproveRejectExist, 'isForApprovalExist' => $isForApprovalExist,
                'action_id'             => $match_data[0]['id'], 'team_a_city' => $team_a_city,
                'team_b_city'           => $team_b_city,
                'rubbers'               => $rubbers, 'active_rubber' => $active_rubber,
                'rubber_details'        => $rubber_details]);


        } else //to view and edit badminton/table badminton score card
        {

            //for form submit pass id from controller
            $form_id = 'badminton';
            return view('scorecards.badminton.badmintonscorecard', [
                'tournamentDetails'     => $tournamentDetails, 'sportsDetails' => $sportsDetails,
                'user_a_name'           => $user_a_name, 'user_b_name' => $user_b_name, 'user_a_logo' => $user_a_logo,
                'user_b_logo'           => $user_b_logo, 'match_data' => $match_data, 'upload_folder' => $upload_folder,
                'is_singles'            => $is_singles, 'score_a_array' => $score_a_array,
                'score_b_array'         => $score_b_array, 'b_players' => $b_players, 'a_players' => $a_players,
                'team_a_player_images'  => $team_a_player_images, 'team_b_player_images' => $team_b_player_images,
                'decoded_match_details' => $decoded_match_details, 'score_status_array' => $score_status_array,
                'loginUserId'           => $loginUserId, 'rej_note_str' => $rej_note_str,
                'loginUserRole'         => $loginUserRole, 'isValidUser' => $isValidUser,
                'isApproveRejectExist'  => $isApproveRejectExist, 'isForApprovalExist' => $isForApprovalExist,
                'action_id'             => $match_data[0]['id'], 'team_a_city' => $team_a_city,
                'team_b_city'           => $team_b_city, 'form_id' => $form_id,
                'rubbers'               => $rubbers, 'active_rubber' => $active_rubber,
                'rubber_details'        => $rubber_details]);

        }

    }

//save or update preferences. 
    public function savePreferences(ObjectRequest $request)
    {
        $match_id = $request->match_id;
        $team_a_name = $request->team_a_name;
        $team_b_name = $request->team_b_name;
        $tournament_id = $request->tournament_id;

        $left_team_id = $request->team_left;
        $right_team_id = $request->team_right;

        $match_model = MatchSchedule::find($match_id);
        $match_model->hasSetupSquad = 1;
        $match_model->save();
        $main_match_model = $match_model;

        $game_type = $match_model->game_type;
        if ($game_type == 'rubber') {
            $active_rubber = $this->getActiveRubber($match_id);
            $rubber_number = $active_rubber->rubber_number;
            $rubber_id = $active_rubber->id;
            $rubber_model = MatchScheduleRubber::find($rubber_id);
            $match_model = $rubber_model;
        } else {
            $rubber_number = 1;
            $rubber_id = null;
        }


        if (!is_null($left_team_id)) {

            $left_team_name = Team::find($left_team_id)->name;
            $right_team_name = Team::find($right_team_id)->name;

            //for match_type player
            $left_team_id_pre = $left_team_id;
            $right_team_id_pre = $right_team_id;

        } else {
            $left_team_name = null;
            $right_team_name = null;

            //match_type player
            $left_team_id_pre = $request->select_player_1_left;
            $right_team_id_pre = $request->select_player_1_right;
        }

        $score_to_win = $request->score_to_win;
        $number_of_sets = $request->number_of_sets;
        $score_to_win = $request->score_to_win;
        $end_point = $request->set_end_point;
        $saving_side = $request->saving_side;
        $enable_two_points = $request->enable_two_points;

        //left players details
        $left_player_1 = $request->select_player_1_left;
        $left_player_1_name = user::find($left_player_1)->name;

        if (!is_null($left_player_2 = $request->select_player_2_left)) $left_player_2_name = user::find($left_player_2)->name;
        else $left_player_2_name = null;

        //right players details
        $right_player_1 = $request->select_player_1_right;
        $right_player_1_name = user::find($right_player_1)->name;

        if (!is_null($right_player_2 = $request->select_player_2_right)) $right_player_2_name = user::find($right_player_2)->name;
        else $right_player_2_name = null;


        $match_details = $match_model->match_details;

        if (empty($match_details)) {
            $match_details = [
                "team_a"        => [                         //left team  of left player for singles
                                                             "id"          => $left_team_id,
                                                             "name"        => $left_team_name,
                                                             "player_1_id" => $left_player_1,
                                                             "player_2_id" => $left_player_2
                ],
                "team_b"        => [                         //right team of right player for player type
                                                             "id"          => $right_team_id,
                                                             "name"        => $right_team_name,
                                                             "player_1_id" => $right_player_1,
                                                             "player_2_id" => $right_player_2
                ],
                "preferences"   => [
                    "left_team_id"      => $left_team_id_pre,
                    "right_team_id"     => $right_team_id_pre,
                    "saving_side"       => "left",
                    "number_of_sets"    => 3,
                    "enable_two_points" => "on",
                    "score_to_win"      => 0,
                    "end_point"         => 0

                ],
                "match_details" => [
                    "set1" => [
                        "{$left_team_id_pre}_score"  => 0,
                        "{$right_team_id_pre}_score" => 0
                    ],
                    "set2" => [
                        "{$left_team_id_pre}_score"  => 0,
                        "{$right_team_id_pre}_score" => 0
                    ],
                    "set3" => [
                        "{$left_team_id_pre}_score"  => 0,
                        "{$right_team_id_pre}_score" => 0
                    ],
                    "set4" => [
                        "{$left_team_id_pre}_score"  => 0,
                        "{$right_team_id_pre}_score" => 0
                    ],
                    "set5" => [
                        "{$left_team_id_pre}_score"  => 0,
                        "{$right_team_id_pre}_score" => 0
                    ]

                ],
                "match_type"    => $match_model->match_type,
                "schedule_type" => $match_model->schedule_type,
                "current_set"   => 1,
                "scores"        => [
                    "{$left_team_id_pre}_score"  => 0,
                    "{$right_team_id_pre}_score" => 0
                ]
            ];

            $match_details = json_encode($match_details);
        }

        $match_details = json_decode($match_details);     //convert it to object to use.

        //set game preferences
        $match_details->preferences->end_point = $end_point;
        $match_details->preferences->score_to_win = $score_to_win;
        $match_details->preferences->number_of_sets = $number_of_sets;
        $match_details->preferences->saving_side = $saving_side;
        $match_details->preferences->enable_two_points = $enable_two_points;
        $match_details->preferences->left_team_id = $left_team_id_pre;
        $match_details->preferences->right_team_id = $right_team_id_pre;

        //player preferences

        $match_details = json_encode($match_details);

        //enter choosen players in the database
        $this->insertBadmintonPlayer($left_player_1, $left_player_2, $tournament_id, $left_team_id, $left_team_name, $match_id, $left_player_1_name, $left_player_2_name, $game_type, $rubber_number, $rubber_id);

        $this->insertBadmintonPlayer($right_player_1, $right_player_2, $tournament_id, $right_team_id, $right_team_name, $match_id, $right_player_1_name, $right_player_2_name, $game_type, $rubber_number, $rubber_id);

        $match_model->hasSetupSquad = 1;
        $match_model->match_details = $match_details;
        $main_match_model->match_details = $match_details;
        $match_model->save();
        $main_match_model->save();

        Helper::start_match_email($match_model);

        return $match_details;
    }

    //insert selected players in the database
    public function insertBadmintonPlayer($player_1_id, $player_2_id, $tournament_id, $team_id, $team_name, $match_id, $player_1_name, $player_2_name, $game_type = 'normal', $rubber_number, $rubber_id)
    {


        if ($game_type != 'rubber') $match_score_model = new BadmintonPlayerMatchScore;
        else    $match_score_model = new BadmintonPlayerRubberScore;


        $match_score_model->user_id_a = $player_1_id;
        $match_score_model->user_id_b = $player_2_id;
        $match_score_model->tournament_id = $tournament_id;
        $match_score_model->team_id = $team_id;
        $match_score_model->team_name = $team_name;
        $match_score_model->match_id = $match_id;
        $match_score_model->player_name_a = $player_1_name;
        $match_score_model->player_name_b = $player_2_name;
        $match_score_model->isactive = 1;

        if ($game_type == 'rubber') {
            $match_score_model->rubber_id = $rubber_id;
            $match_score_model->rubber_number = $rubber_number;
        }


        $match_score_model->save();
    }

    //add scores

    public function addScore(ObjectRequest $request)
    {


        $match_id = $request->match_id;
        $table_score_id = $request->table_score_id;
        $team_id = (int)$request->team_id;
        $action = $request->action;    //add or remove;

        $match_model = MatchSchedule::find($match_id);            //match_schedule data
        $game_type = $match_model->game_type;
        $match_details = json_decode($match_model->match_details);
        $preferences = $match_details->preferences;

        //opponent team data

        $end_point = $preferences->end_point;
        $score_to_win = $preferences->score_to_win;
        $number_of_sets = $preferences->number_of_sets;
        $saving_side = $preferences->saving_side;
        $enable_two_points = $preferences->enable_two_points;

        if ($game_type == 'normal') {
            $match_model = MatchSchedule::find($match_id);
            $left_player_model = BadmintonPlayerMatchScore::find($table_score_id);
            $right_player_model = BadmintonPlayerMatchScore::where('match_id', $match_id)
                                                           ->where('id', '!=', $table_score_id)->first();
        } else {

            $active_rubber = $this->getActiveRubber($match_id);
            $rubber_id = $active_rubber->id;

            //get the match model from rubber;
            $match_id = $rubber_id;
            $match_model = MatchScheduleRubber::find($match_id);
            $match_details = json_decode($match_model->match_details);
            $match_model = MatchScheduleRubber::find($match_id);
            $left_player_model = BadmintonPlayerRubberScore::find($table_score_id);
            $right_player_model = BadmintonPlayerRubberScore::where('rubber_id', $match_id)
                                                            ->where('id', '!=', $table_score_id)->first();
        }


        $match_score_model = $left_player_model;
        $match_score_model_other = $right_player_model;


        if ($this->checkSet('set1', $left_player_model, $right_player_model, $preferences)) {

            if ($action == 'remove' && $match_score_model->set1 > 0) {   //remove point if set_score>0
                $match_score_model->set1--;
                $match_score_model->save();

                $match_details->match_details->set1->{$team_id . "_score"} = $match_score_model->set1;

            } elseif ($action == 'add') {

                $match_score_model->set1++;
                $match_score_model->save();

                $match_details->match_details->set1->{$team_id . "_score"} = $match_score_model->set1;
            }
        } else {           //set1 is complete

            if ($this->checkSet('set2', $left_player_model, $right_player_model, $preferences)) {
                if ($action == 'remove' && $match_score_model->set2 > 0) {
                    $match_score_model->set2--;
                    $match_score_model->save();
                    $match_details->match_details->set2->{$team_id . "_score"} = $match_score_model->set2;
                } elseif ($action == 'add') {
                    $match_score_model->set2++;
                    $match_score_model->save();
                    $match_details->match_details->set2->{$team_id . "_score"} = $match_score_model->set2;

                }
            } else {       //set2 is complete
                if ($this->checkSet('set3', $match_score_model, $match_score_model_other, $preferences)) {
                    if ($action == 'remove' && $match_score_model->set3 > 0) {
                        $match_score_model->set3--;
                        $match_score_model->save();
                        $match_details->match_details->set3->{$team_id . "_score"} = $match_score_model->set3;

                    } elseif ($action == 'add') {
                        $match_score_model->set3++;
                        $match_score_model->save();
                        $match_details->match_details->set3->{$team_id . "_score"} = $match_score_model->set3;

                    }
                } else {

                    if ($number_of_sets > 3) {

                        if ($this->checkSet('set4', $match_score_model, $match_score_model_other, $preferences)) {
                            if ($action == 'remove' && $match_score_model->set4 > 0) {

                                $match_score_model->set4--;
                                $match_score_model->save();

                                $match_details->match_details->set4->{$team_id . "_score"} = $match_score_model->set4;
                            } elseif ($action == 'add') {
                                $match_score_model->set4++;
                                $match_score_model->save();
                                $match_details->match_details->set4->{$team_id . "_score"} = $match_score_model->set4;
                            }
                        } else {
                            if ($this->checkSet('set5', $match_score_model, $match_score_model_other, $preferences)) {
                                if ($action == 'remove' && $match_score_model->set5 > 0) {

                                    $match_score_model->set5--;
                                    $match_score_model->save();
                                    $match_details->match_details->set5->{$team_id . "_score"} = $match_score_model->set5;

                                } elseif ($action == 'add') {
                                    $match_score_model->set5++;
                                    $match_score_model->save();
                                    $match_details->match_details->set5->{$team_id . "_score"} = $match_score_model->set5;
                                }
                            }
                        }

                    }
                }
            }
        }

        $match_details->current_set = $this->getCurrentSet($match_id, $game_type);
        $match_details->scores = $this->getScoreSet($match_id, $game_type);

        $match_details = json_encode($match_details);
        $match_model->match_details = $match_details;
        $match_model->save();
        $match_score_model->save();


        if ($match_model->match_type != 'singles') $player_ids = [
            $match_score_model->user_id_a, $match_score_model->user_id_b];
        else $player_ids = [$match_score_model->user_id_a];

        $this->badmintonStatistics($player_ids, $match_model->match_type, '', $match_model->schedule_type);

        return $match_details;
    }

    //check if a set is full or complete. returns true for not complete returns false for complete

    public function checkSet($set, $match_score_model, $match_score_model_other, $preferences)
    {
        $end_point = $preferences->end_point;
        $score_to_win = $preferences->score_to_win;
        $number_of_sets = $preferences->number_of_sets;
        $saving_side = $preferences->saving_side;
        $enable_two_points = $preferences->enable_two_points;

        $set1_score = $match_score_model->{$set};
        $set1_opponent_score = $match_score_model_other->{$set};

        if ($set1_score < $score_to_win && $set1_opponent_score < $score_to_win) {
            return true;
            //if user one and two scores are less than the score to win, active set
        } else if ($set1_score == $end_point || $set1_opponent_score == $end_point) {
            return false;
            //if a user score = end_point score, skip set. or complete set.
        } else if ($set1_score >= $score_to_win || $set1_opponent_score >= $score_to_win) {
            if ($enable_two_points == 'on') {
                if (($set1_score - $set1_opponent_score) >= 2) return false;
                elseif (($set1_opponent_score - $set1_score) >= 2) return false;
                else return true;
            } else {
                return false;
            }
        }


    }

    public function getCurrentSet($match_id, $game_type = 'normal')
    {


        //first and second player(team) or left and right player(team)


        if ($game_type == 'normal') {
            $match_model = MatchSchedule::find($match_id);
            $left_player_model = BadmintonPlayerMatchScore::where('match_id', $match_id)->first();
            $right_player_model = BadmintonPlayerMatchScore::where('match_id', $match_id)->skip(1)->first();
        } else {
            $match_model = MatchScheduleRubber::find($match_id);
            $left_player_model = BadmintonPlayerRubberScore::where('rubber_id', $match_id)->first();
            $right_player_model = BadmintonPlayerRubberScore::where('rubber_id', $match_id)->skip(1)->first();
        }

        $match_score_model = $left_player_model;
        $match_score_model_other = $right_player_model;


        $preferences = json_decode($match_model->match_details)->preferences;


        if ($this->checkSet('set1', $match_score_model, $match_score_model_other, $preferences)) {
            return 1;
        } else {           //set1 is complete

            if ($this->checkSet('set2', $match_score_model, $match_score_model_other, $preferences)) {
                return 2;
            } else {       //set2 is complete
                if ($this->checkSet('set3', $match_score_model, $match_score_model_other, $preferences)) {
                    return 3;
                } else {

                    if ($this->checkSet('set4', $match_score_model, $match_score_model_other, $preferences)) {
                        return 4;

                    } else {
                        if ($this->checkSet('set5', $match_score_model, $match_score_model_other, $preferences)) {
                            return 5;
                        } else return 0;
                    }

                }
            }
        }
    }


    //save record manually;

    public function getScoreSet($match_id, $game_type = 'normal')
    {

        if ($game_type == 'normal') {
            $match_model = MatchSchedule::find($match_id);
            $left_player_model = BadmintonPlayerMatchScore::where('match_id', $match_id)->first();
            $right_player_model = BadmintonPlayerMatchScore::where('match_id', $match_id)->skip(1)->first();
        } else {
            $match_model = MatchScheduleRubber::find($match_id);
            $left_player_model = BadmintonPlayerRubberScore::where('rubber_id', $match_id)->first();
            $right_player_model = BadmintonPlayerRubberScore::where('rubber_id', $match_id)->skip(1)->first();
        }

        $left_player_score = 0;
        $right_player_score = 0;

        for ($i = 1; $i <= 5; $i++) {
            if ($left_player_model->{'set' . $i} == $right_player_model->{'set' . $i}) {
                //nothing to do
            } elseif ($left_player_model->{'set' . $i} > $right_player_model->{'set' . $i}) {
                $left_player_score++;
            } elseif ($left_player_model->{'set' . $i} < $right_player_model->{'set' . $i}) {
                $right_player_score++;
            }
        }

        $team_a_id = $left_player_model->team_id == null ? $left_player_model->user_id_a : $left_player_model->team_id;
        $team_b_id = $right_player_model->team_id == null ? $right_player_model->user_id_a : $right_player_model->team_id;


        return [
            "{$team_a_id}_score" => $left_player_score,
            "{$team_b_id}_score" => $right_player_score
        ];
    }


//get json details;

    public function badmintonStatistics($player_ids_array, $match_type, $is_win = '', $schedule_type)
    {
        //$player_ids_array = explode(',',$player_ids);
        foreach ($player_ids_array as $user_id) {
            if (!empty($user_id)) {
                $double_faults_count = '';

                $player_match_details = BadmintonPlayerMatchScore::where('user_id_a', $user_id)
                                                                 ->orWhere('user_id_b', $user_id)->get();
                $player_rubber_details = BadmintonPlayerRubberScore::where('user_id_a', $user_id)
                                                                   ->orWhere('user_id_b', $user_id)->get();

                //check already user id exists or not
                $badminton_statistics = BadmintonStatistic::whereUserId($user_id)->whereMatchType($match_type)->first();

                if (!is_null($badminton_statistics)) {  //if user exist update statistics
                    $won = $badminton_statistics->won;
                    $lost = $badminton_statistics->lost;

                    if ($is_win == 'yes') {
                        $won++;
                    } elseif ($is_win == 'no') {
                        $lost++;
                    }


                    $matches = count($player_match_details) + count($player_rubber_details);
                    $won_percentage = number_format((($won + 1) / ($matches + 1)) * 100, 2);

                    $badminton_statistics->matches = $matches;
                    $badminton_statistics->won = $won;
                    $badminton_statistics->lost = $lost;
                    $badminton_statistics->won_percentage = $won_percentage;
                    $badminton_statistics->save();
                } else {                                   //else create new statistics
                    $won = '';
                    $won_percentage = '';
                    $lost = '';
                    if ($is_win == 'yes') //win count
                    {
                        $won = 1;
                        $won_percentage = number_format(100, 2);
                    } else if ($is_win == 'no') //lost count
                    {
                        $lost = 1;
                    }
                    $badmintonStatisticsModel = new BadmintonStatistic();
                    $badmintonStatisticsModel->user_id = $user_id;
                    $badmintonStatisticsModel->match_type = $match_type;
                    $badmintonStatisticsModel->matches = 1;
                    $badmintonStatisticsModel->won_percentage = $won_percentage;
                    $badmintonStatisticsModel->won = $won;
                    $badmintonStatisticsModel->lost = $lost;

                    $badmintonStatisticsModel->save();
                }
            }

        }

    }

//store final record. When clicked on end match button //sets the winner and loser

    public function manualScoring(ObjectRequest $request)
    {
        $score_a_id = $request->score_a_id;
        $score_b_id = $request->score_b_id;
        $number_of_sets = $request->number_of_sets;
        $match_id = $request->match_id;
        $match_model = Matchschedule::find($match_id);
        $game_type = $match_model->game_type;


        if ($game_type == 'rubber') {
            $score_a_model = BadmintonPlayerRubberScore::find($score_a_id);
            $score_b_model = BadmintonPlayerRubberScore::find($score_b_id);
            $match_id = $score_a_model->rubber_id;
            $match_model = MatchScheduleRubber::find($match_id);

        } else {
            $score_a_model = BadmintonPlayerMatchScore::find($score_a_id);
            $score_b_model = BadmintonPlayerMatchScore::find($score_b_id);
            $match_id = $score_a_model->match_id;
            $match_model = Matchschedule::find($match_id);
        }


        $match_details = json_decode($match_model->match_details);
        $match_details_data = $match_details->match_details;

        $left_team_id = $match_details->preferences->left_team_id;
        $right_team_id = $match_details->preferences->right_team_id;
        $end_point = $match_details->preferences->end_point;

        //start scoring

        for ($i = 1; $i <= $number_of_sets; $i++) {
            $score_a_model->{"set" . $i} = $request->{"a_set" . $i} > $end_point ? $end_point : (int)$request->{"a_set" . $i};
            $score_b_model->{"set" . $i} = $request->{"b_set" . $i} > $end_point ? $end_point : (int)$request->{"b_set" . $i};

            $match_details_data->{"set" . $i}->{$left_team_id . "_score"} = (int)$request->{"a_set" . $i} > $end_point ? $end_point : (int)$request->{"a_set" . $i};
            $match_details_data->{"set" . $i}->{$right_team_id . "_score"} = (int)$request->{"b_set" . $i} > $end_point ? $end_point : (int)$request->{"b_set" . $i};
        }

        $score_a_model->save();
        $score_b_model->save();

        $match_details->match_details = $match_details_data;
        $match_details->scores = $this->getScoreSet($match_id, $game_type);
        $match_details->current_set = $this->getCurrentSet($match_id, $game_type);     //get current active set

        $match_model->match_details = json_encode($match_details);
        $match_model->save();

        $this->deny_match_edit_by_admin();

        return 'match saved';
    }

    public function getBadmintonDetails(ObjectRequest $request)
    {
        $match_id = $request->match_id;
        $match_model = matchschedule::find($match_id);
        return $match_model->match_details;
    }

    public function badmintonStoreRecord(ObjectRequest $Objrequest)
    {

        $loginUserId = Auth::user()->id;
        $request = Request::all();
        $tournament_id = !empty(Request::get('tournament_id')) ? Request::get('tournament_id') : NULL;
        $match_id = !empty(Request::get('match_id')) ? Request::get('match_id') : NULL;
        $match_data = MatchSchedule::find($match_id);
        $match_result = !empty(Request::get('match_result')) ? Request::get('match_result') : NULL;
        $match_type = !empty(Request::get('match_type')) ? Request::get('match_type') : NULL;
        $player_ids_a = !empty(Request::get('player_ids_a')) ? Request::get('player_ids_a') : NULL;
        $player_ids_b = !empty(Request::get('player_ids_b')) ? Request::get('player_ids_b') : NULL;
        $is_singles = !empty(Request::get('is_singles')) ? Request::get('is_singles') : NULL;
        $is_winner_inserted = !empty(Request::get('is_winner_inserted')) ? Request::get('is_winner_inserted') : NULL;
        $winner_team_id = !empty(Request::get('winner_team_id')) ? Request::get('winner_team_id') : $is_winner_inserted;//winner_id

        $team_a_players = !empty(Request::get('a_player_ids')) ? Request::get('a_player_ids') : [];//player id if match type is singles
        $team_b_players = !empty(Request::get('b_player_ids')) ? Request::get('b_player_ids') : [];//player id if match type is singles

        $schedule_type = !empty(Request::get('schedule_type')) ? Request::get('schedule_type') : NULL;

        $match_model = Matchschedule::find($match_id);
        $game_type = $match_model->game_type;

        if ($game_type == 'rubber') {
            $number_of_rubber = $match_model->number_of_rubber;
            $active_rubber = $this->getActiveRubber($match_id);
            $rubber_number = $active_rubber->rubber_number;

            if ($number_of_rubber == $rubber_number) $rubber_completed = 0;
            else $rubber_completed = 0;
            $rubber_id = $active_rubber->id;
            $this->destroyRubberFromSession();
        } else {
            $rubber_completed = 0;
            $rubber_id = 1;
        }


        //get previous scorecard status data
        if ($game_type == 'normal') $scorecardDetails = MatchSchedule::where('id', $match_id)->pluck('score_added_by');
        else  $scorecardDetails = MatchScheduleRubber::where('id', $rubber_id)->pluck('score_added_by');


        $decode_scorecard_data = json_decode($scorecardDetails, true);

        $modified_users = !empty($decode_scorecard_data['modified_users']) ? $decode_scorecard_data['modified_users'] : '';

        $modified_users = $modified_users . ',' . $loginUserId;//scorecard changed users

        $added_by = !empty($decode_scorecard_data['added_by']) ? $decode_scorecard_data['added_by'] : $loginUserId;

        //score card approval process
        $score_status = [
            'added_by'      => $added_by, 'active_user' => $loginUserId, 'modified_users' => $modified_users,
            'rejected_note' => ''];

        $json_score_status = json_encode($score_status);


        $is_tie = ($match_result == 'tie') ? 1 : 0;
        $is_washout = ($match_result == 'washout') ? 1 : 0;
        $has_result = ($is_washout == 1) ? 0 : 1;
        $match_result = (!in_array($match_result, ['tie', 'win', 'washout'])) ? NULL : $match_result;


        //update winner id
        if ($game_type == 'normal') $matchScheduleDetails = MatchSchedule::where('id', $match_id)->first();
        else $matchScheduleDetails = MatchScheduleRubber::where('id', $rubber_id)->first();

        if (count($matchScheduleDetails)) {
            $looser_team_id = NULL;
            $match_status = 'scheduled';
            $approved = '';
            if ($is_tie == 0 || $is_washout == 0) {
                if (isset($winner_team_id)) {
                    if ($winner_team_id == $matchScheduleDetails['a_id']) {
                        $looser_team_id = $matchScheduleDetails['b_id'];
                    } else {
                        $looser_team_id = $matchScheduleDetails['a_id'];
                    }
                    $match_status = 'completed';
                    $approved = 'approved';
                }
            }

            $this->deny_match_edit_by_admin();

            if (!empty($matchScheduleDetails['tournament_id'])) {
                $tournamentDetails = Tournaments::where('id', '=', $matchScheduleDetails['tournament_id'])->first();
                $match_status = 'completed';
                if (Helper::isTournamentOwner($tournamentDetails['manager_id'], $tournamentDetails['tournament_parent_id'])) {

                    if ($game_type == 'normal') {
                        MatchSchedule::where('id', $match_id)->update([
                            'match_status'   => $match_status,
                            'winner_id'      => $winner_team_id, 'looser_id' => $looser_team_id,
                            'has_result'     => $has_result,
                            'match_result'   => $match_result,
                            'score_added_by' => $json_score_status]);
                        if (!empty($matchScheduleDetails['tournament_round_number'])) {
                            $matchScheduleDetails->updateBracketDetails();
                        }
                        if ($match_status == 'completed') {

                            $sportName = Sport::where('id', $matchScheduleDetails['sports_id'])->pluck('sports_name');
                            $this->insertPlayerStatistics($sportName, $match_id);
                            $this->updateStatitics($match_id, $winner_team_id, $looser_team_id);
                        }

                    } else {
                        MatchScheduleRubber::where('id', $rubber_id)->update([
                            'match_status'   => $match_status,
                            'winner_id'      => $winner_team_id, 'looser_id' => $looser_team_id,
                            'has_result'     => $has_result,
                            'match_result'   => $match_result,
                            'score_added_by' => $json_score_status]);

//                                Helper::printQueries();

                        if ($rubber_completed && $match_status == 'completed') {
                            $winners_from_rubber = ScoreCard::getWinnerInRubber($match_id, $match_model->sports_id);
                            $winner_team_id = $winners_from_rubber['winner'];
                            $looser_team_id = $winners_from_rubber['looser'];

                            MatchSchedule::where('id', $match_id)->update([
                                'match_status'   => $match_status,
                                'winner_id'      => $winner_team_id, 'looser_id' => $looser_team_id,
                                'has_result'     => $has_result,
                                'match_result'   => $match_result,
                                'score_added_by' => $json_score_status]);

                            if (!empty($matchScheduleDetails['tournament_round_number'])) {
                                $matchScheduleDetails->updateBracketDetails();
                            }

                            $sportName = Sport::where('id', $match_model->sports_id)->pluck('sports_name');
                            $this->insertPlayerStatistics($sportName, $match_id);
                            $this->updateStatitics($match_id, $winner_team_id, $looser_team_id);
                        }
                    }


                }


            } else if (Auth::user()->role == 'admin') {

                if ($is_tie == 1 || $match_result == "washout") {
                    $match_status = 'completed';
                    $approved = 'approved';
                }

                if ($game_type == 'normal') {
                    MatchSchedule::where('id', $match_id)->update([
                        'match_status'   => $match_status,
                        'winner_id'      => $winner_team_id, 'looser_id' => $looser_team_id,
                        'has_result'     => $has_result,
                        'is_tied'        => $is_tie,
                        'match_result'   => $match_result,
                        'score_added_by' => $json_score_status, 'scoring_status' => $approved]);

                    if ($match_status == 'completed') {
                        $sportName = Sport::where('id', $matchScheduleDetails['sports_id'])->pluck('sports_name');
                        $this->insertPlayerStatistics($sportName, $match_id);
                        $this->updateStatitics($match_id, $winner_team_id, $looser_team_id);

                    }
                } else {
                    MatchScheduleRubber::where('id', $rubber_id)->update([
                        'match_status'   => $match_status,
                        'winner_id'      => $winner_team_id, 'looser_id' => $looser_team_id,
                        'has_result'     => $has_result,
                        'is_tied'        => $is_tie,
                        'match_result'   => $match_result,
                        'score_added_by' => $json_score_status]);

                    if ($rubber_completed && $match_status == 'completed') {
                        $winners_from_rubber = ScoreCard::getWinnerInRubber($match_id, $match_model->sports_id);
                        $winner_team_id = $winners_from_rubber['winner'];
                        $looser_team_id = $winners_from_rubber['looser'];
                        MatchSchedule::where('id', $match_id)->update([
                            'match_status'   => $match_status,
                            'winner_id'      => $winner_team_id, 'looser_id' => $looser_team_id,
                            'has_result'     => $has_result,
                            'is_tied'        => $is_tie,
                            'match_result'   => $match_result,
                            'score_added_by' => $json_score_status,
                            'scoring_status' => $approved]);


                        if (!empty($matchScheduleDetails['tournament_round_number'])) {
                            $matchScheduleDetails->updateBracketDetails();
                        }

                        $sportName = Sport::where('id', $match_model->sports_id)->pluck('sports_name');
                        $this->insertPlayerStatistics($sportName, $match_id);
                        $this->updateStatitics($match_id, $winner_team_id, $looser_team_id);
                    }
                }
            } else {
                MatchSchedule::where('id', $match_id)->update([
                    'winner_id'      => $winner_team_id,
                    'looser_id'      => $looser_team_id,
                    'has_result'     => $has_result,
                    'is_tied'        => $is_tie,
                    'match_result'   => $match_result,
                    'score_added_by' => $json_score_status]);

                $this->updateStatitics($match_id, $winner_team_id, $looser_team_id);
            }
        }
        //MatchSchedule::where('id',$match_id)->update(['winner_id'=>$winner_team_id,'match_details'=>$json_match_details_array,'score_added_by'=>$json_score_status ]);
        //if($winner_team_id>0)
        //return redirect()->route('match/scorecard/view', [$match_id])->with('status', trans('message.scorecard.scorecardmsg'));

        Scorecard::getWinnerInRubber($match_id, $sports_id = 5);
        return redirect()->back()->with('status', trans('message.scorecard.scorecardmsg'));

    }

    //get the scores from number of sets winned.

    public function updateStatitics($match_id, $winner_team_id, $looser_team_id)
    {


        $match_model = MatchSchedule::find($match_id);
        $player_ids = explode(',', $match_model->player_a_ids);
        //get winner of looser;
        if ($match_model->a_id == $winner_team_id) {
            $is_win = 'yes';
        } elseif ($match_model->a_id = $looser_team_id) {
            $is_win = 'no';
        } else $is_win = '';

        $this->badmintonStatistics($player_ids, $match_model->match_type, $is_win, $match_model->schedule_type);

        $match_model = MatchSchedule::find($match_id);
        $player_ids = explode(',', $match_model->player_b_ids);

        if ($match_model->b_id == $winner_team_id) {
            $is_win = 'yes';
        } elseif ($match_model->b_id = $looser_team_id) {
            $is_win = 'no';
        } else $is_win = '';
        $this->badmintonStatistics($player_ids, $match_model->match_type, $is_win, $match_model->schedule_type);

    }

    public function updatePreferences(ObjectRequest $request)
    {
        $match_id = $request->match_id;
        $rubber_id = $request->rubber_id;

        $match_model = MatchSchedule::find($match_id);
        if ($match_model->game_type == "rubber") $match_model = MatchScheduleRubber::find($rubber_id);

        $match_details = json_decode($match_model->match_details);

        $preferences = $match_details->preferences;

        $preferences->number_of_sets = $request->number_of_sets;
        $preferences->end_point = $request->set_end_point;
        $preferences->score_to_win = $request->score_to_win;
        $preferences->enable_two_points = $request->enable_two_points;

        $match_details->preferences = $preferences;

        $match_details = json_encode($match_details);

        $match_model->match_details = $match_details;

        $match_model->save();

        return "preferences updated";
    }

    public function discardMatchRecords($players_stats)
    {
        foreach ($players_stats as $ps) {
            //$ps->delete();
        }
    }

    //end match for rubber type, even if all rubbers is not played
    public function endMatchCompletely($match_id)
    {
        $loginUserId = Auth::user()->id;
        $match_model = MatchSchedule::find($match_id);
        $tournamentDetails = Tournaments::find($match_model->tournament_id);
        $winners_from_rubber = ScoreCard::getWinnerInRubber($match_id, $match_model->sports_id);

        $scorecardDetails = MatchSchedule::where('id', $match_id)->pluck('score_added_by');
        $decode_scorecard_data = json_decode($scorecardDetails, true);
        $modified_users = !empty($decode_scorecard_data['modified_users']) ? $decode_scorecard_data['modified_users'] : '';
        $modified_users = $modified_users . ',' . $loginUserId;//scorecard changed users
        $added_by = !empty($decode_scorecard_data['added_by']) ? $decode_scorecard_data['added_by'] : $loginUserId;
        //score card approval process
        $score_status = [
            'added_by'      => $added_by, 'active_user' => $loginUserId, 'modified_users' => $modified_users,
            'rejected_note' => ''];

        $json_score_status = json_encode($score_status);
        $winner_team_id = $winners_from_rubber['winner'];
        $looser_team_id = $winners_from_rubber['looser'];

        MatchSchedule::where('id', $match_id)->update([
            'match_status'   => 'completed',
            'winner_id'      => $winner_team_id, 'looser_id' => $looser_team_id,
            'has_result'     => 1,
            'match_result'   => 'win',
            'score_added_by' => $json_score_status]);

        if (!empty($match_model->tournament_round_number)) {
            $match_model->updateBracketDetails();
        }

        $sportName = Sport::where('id', $match_model->sports_id)->pluck('sports_name');
        $this->insertPlayerStatistics($sportName, $match_id);
        $this->updateStatitics($match_id, $winner_team_id, $looser_team_id);

        $this->move_forward_schedule( $match_id  , $winner_team_id , $looser_team_id  );
    }
    

    private function move_forward_schedule( $match_id , $winner_team_id , $looser_team_id )
    {
            $match_data = MatchSchedule::where('id',$match_id)->first();
            // winner go 
            if( isset( $match_data['winner_schedule_id'] ) && $match_data['winner_schedule_id'] * 1 > 0 ) 
            {
                $ab_id = $match_data['winner_schedule_position']."_id";
                MatchSchedule::where('id' , $match_data['winner_schedule_id'] )->update( [ $ab_id=>$winner_team_id ] );
            }

            if( isset( $match_data['loser_schedule_id'] ) && $match_data['loser_schedule_id'] * 1 > 0 ) 
            {
                $ab_id = $match_data['loser_schedule_position']."_id";
                if( $ab_id == 'a' || $ab_id == 'b' )
                    MatchSchedule::where('id' , $match_data['loser_schedule_id'] )->update( [ $ab_id=>$looser_team_id ] );
            }
    }



}




