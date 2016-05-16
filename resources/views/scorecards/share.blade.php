<?php 

// $user_a_name is used in tennis scorecard view
$team_a_name = isset($user_a_name) ? $user_a_name : $team_a_name;
$team_b_name = isset($user_b_name) ? $user_b_name : $team_b_name;
$team_share_title       = $sportsDetails[0]['sports_name'] . ' Scorecard for ' . $team_a_name . ' Vs ' . $team_b_name;                                                
$team_share_desc        = $sportsDetails[0]['sports_name'] . ' Scorecard for ' . $team_a_name . ' Vs ' . $team_b_name . ' played at ' . (($match_data[0]['facility_name']!='') ? ' , '.$match_data[0]['facility_name']:'').(($match_data[0]['address']!='')?' , '.$match_data[0]['address']:'') . ' on ' . date('jS F , Y', strtotime($match_data[0]['match_start_date']));
if (isset($tournamentDetails['tournament_parent_name']) && !empty($tournamentDetails['tournament_parent_name']))
{
        $team_share_desc = $tournamentDetails['tournament_parent_name'] . ': ' . $team_share_desc;
}
$team_share_desc_encoded = urlencode($team_share_desc);

$fb_url = 'https://www.facebook.com/sharer.php?u=' . url('matchpublic/scorecard/view',$match_data[0]['id']) . '&amp;text=' . $team_share_desc_encoded;
$tw_url = 'https://twitter.com/intent/tweet?url=' . url('matchpublic/scorecard/view',$match_data[0]['id']) . '&amp;text=' . $team_share_desc_encoded . '&amp;via=sj_sportsjun';
$gp_url = 'https://plus.google.com/share?url=' . url('matchpublic/scorecard/view',$match_data[0]['id']);
?>
<div class="share-scorecard pull-left">
        <table class="sj-social">
                <tbody>
                        <tr>
                                <td class="sj-social-td">
                                        <a target="_blank" href="{{$fb_url}}" class="sj-social-ancr sj-social-ancr-fb" rel="noreferrer">
                                                <span class="sj-ico sj-fb-share "></span>
                                                <span class="sj-font-12">Share</span>
                                        </a>
                                </td>
                                <td class="sj-social-td">
                                        <a target="_blank" href="{{$tw_url}}" class="sj-social-ancr sj-social-ancr-twt" rel="noreferrer">
                                                <span class="sj-ico sj-twt-share"></span>
                                                <span class="sj-font-12">Tweet</span>
                                        </a>
                                </td>
                                <td class="sj-social-td">
                                        <a target="_blank" href="{{$gp_url}}" class="sj-social-ancr sj-social-ancr-gplus" rel="noreferrer">
                                                <span class="sj-ico sj-gplus-share"></span>
                                                <span class="sj-font-12">Share</span>
                                        </a>
                                </td>
                        </tr>
                </tbody>
        </table>
</div>