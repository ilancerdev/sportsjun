<?php 

$t_url=url("/viewpublic/gettournamentdetails/{$tournamentInfo[0]['id']}");
$t_text="{$tournamentInfo[0]['name']} is a match tournament with {$tournamentInfo[0]['prize_money']} worth. {$tournamentInfo[0]['name']} starts from {$tournamentInfo[0]['start_date']} to {$tournamentInfo[0]['end_date']} at {$tournamentInfo[0]['location']}  {$tournamentInfo[0]['description']}";
$t_title="Tournament Details for {$tournamentInfo[0]['name']}";
$t_img=url("/uploads/tournaments/{$tournamentInfo[0]['logo']}");
?>                      
<meta property="og:url"           content="{{$t_url}}" />
<meta property="og:type"          content="website" />
<meta property="og:title"         content="<?php echo $t_title ?>" />
<meta property="og:description"   content="<?php echo $t_text ?>" />
<meta property="og:image"         content="{{$t_img }}" />
<meta property="og:image"         content="{{ asset('/images/sj_facebook_share.jpg') }}" />
