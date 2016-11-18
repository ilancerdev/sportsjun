<?php
//Scorecards
/* Route::get('public/completedmatches', [
	'as' => 'public/completedmatches', 'uses' =>  'Auth\ScoreCardController@completedMatches'
	]);
	
Route::get('public/scorecard/view/{match_id}', [
			'as' => 'public/scorecard/view', 'uses' => 'Auth\ScoreCardController@createScorecardView'
	]); */

Route::get('matchpublic/scorecard/view/{match_id}', [
    'as' => 'matchpublic/scorecard/view',
    'uses' => 'User\ScoreCardController@createScorecardpublicView'
]);

Route::group([],function($route){
    $route->get('organization/{id}/teamlist', [
        'as'   => 'organization.members.teamlist',
        'uses' => 'User\OrganizationMembersController@teamList',
    ]);

    Route::get('organization/{id}/tournamentlist', [
        'as'   => 'organization.schedules.tournamentlist',
        'uses' => 'User\OrganizationSchedulesController@tournamentList',
    ]);
});





Route::group(['prefix' => 'viewpublic'], function () {
    Route::get('createphoto/{album_id}/{user_id}/{is_user?}/{action?}/{team_id?}/{page?}',
        'User\AlbumController@createphotopublic');
    Route::get('user/album/show/{test?}/{id?}/{team_id?}/{page?}', 'User\AlbumController@show');
    Route::get('gettournamentdetails/{id}', 'User\TournamentsController@getTournamentDetails');
    Route::get('tournaments/groups/{id}/player_standing', 'User\TournamentsController@playerStanding');
    Route::get('tournaments/groups/{id}/{type?}', 'User\TournamentsController@groups');
    Route::get('editsportprofile/{userId}', 'User\SportController@editSportProfile');
    Route::get('showsportprofile/{userId}', 'User\SportController@showSportProfile');
    Route::get('team/members/{team_id}/{status?}', 'User\TeamController@myteam');

    Route::get('getquestions', 'User\SportController@getQuestions');

    // Routes for match details loaded with ajax

    Route::get('/match/getBadmintonDetails', 'User\ScoreCard\BadmintonScoreCardController@getBadmintonDetails');
    Route::get('/match/getSoccerDetails', 'User\ScoreCardController@getSoccerDetails');

    //Route::get('organization/{slug}','User\TeamController@getorgDetails');
    Route::get('organization/{id}', 'User\TeamController@getorgDetails');

    Route::group(['as' => 'widget.','namespace'=>'Widget'], function ($route) {
        $route->group(['as' => 'organization.'], function ($route) {
            $route->get('organization/{id}/widget/info', ['as' => 'info', 'uses' => 'OrganizationController@show']);
            $route->get('organization/{id}/widget/staff',
                ['as' => 'staff', 'uses' => 'OrganizationController@staff']);
            $route->get('organization/{id}/widget/groups',
                ['as' => 'groups', 'uses' => 'OrganizationController@groups']);
            $route->get('organization/{id}/widget/members',
                ['as' => 'members', 'uses' => 'OrganizationController@members']);
            $route->get('organization/{id}/widget/tournaments',
                ['as' => 'tournaments', 'uses' => 'OrganizationController@tournaments']);
            $route->get('organization/{id}/widget/schedule',
                ['as' => 'schedule', 'uses' => 'OrganizationController@schedule']);
            $route->get('organization/{id}/widget/gallery',
                ['as' => 'gallery', 'uses' => 'OrganizationController@gallery']);

            $route->get('organization/{id}/teams/{group_id}',
                ['as' => 'teams', 'uses'=>'OrganizationController@teams']
            );
        });
        $route->get('team/{id}/widget/info/{status?}',
            ['as' => 'team.info', 'uses'=>'TeamController@show']
        );

        $route->get('member/{id}/widget/info',
            ['as' => 'member.info', 'uses'=>'MemberController@show']
        );

        $route->get('formgallery/{id?}/{action?}/widget', [
            'as'   => 'formgallery',
            'uses' => 'AlbumController@Formgallery',
        ]);

        $route->get('createphoto/{album_id}/{user_id}/{is_user?}/{action?}/{team_id?}/{page?}',
            [
                'as'   => 'createphoto',
                'uses' => 'AlbumController@createphoto',
            ]);


        $route->get('match/{match_id}/scorecard/widget/view', [
            'as'   => 'match.scorecard.view',
            'uses' => 'ScoreCardController@createScorecardView',
        ]);
    });


});


//Player Stats

//Marketplace

//Tournaments

//Login/Signup pages
/*
Route::get('/', function () {
    return view('welcome');
});
 * 
 */
Route::get('js_close', function () {
    return "<script type=\"text/javascript\">window.close();</script>";
});
Route::get('testshare', array(
    'as' => 'testshare',
    function () {
        return 'Hello World!!!';
    }
));
Route::get('/', 'HomeController@index');
Route::get('/{page}.html', 'HomeController@page');
Route::get('/skip', 'HomeController@skip');

Route::get('/home/search/guess',['as'=>'public.search.guess','uses'=>'SearchController@searchGuess']);
Route::get('/home/search',['as'=>'public.search.list','uses'=>'SearchController@search']);
Route::get('/home/{type}/{id}',['as'=>'public.search.view','uses'=>'SearchController@view'])->where('id', '[0-9]+');


// Default Laravel Routes for login,registration and reset password
Route::controllers([
    'auth' => 'Auth\AuthController',
    'password' => 'Auth\PasswordController',
]);
//Start Auth
Route::get('verifyemail', [
    'as' => 'verifyemail',
    'uses' => 'Auth\AuthController@verifyEmail'
]);

Route::get('confirmemail/{code}', [
    'as' => 'confirmemail/{code}',
    'uses' => 'Auth\AuthController@confirmEmail'
]);
Route::get('resendverifyemail/{email}', [
    'as' => 'resendverifyemail/{email}',
    'uses' => 'Auth\AuthController@resendVerifyEmail'
]);
//End auth
Route::get('refereshcapcha', [
    'as' => 'refereshcapcha',
    'uses' => 'Auth\AuthController@refereshcapcha'
]);

//START CRONS
Route::get('/schedulescron', function () {
    $exitCode = Artisan::call('cron:notifyschedules');
    //
});
Route::get('/mailscron', function () {
    $exitCode = Artisan::call('cron:sendmails');
    //
});
//END CRONS

//Login/Signup
Route::get('social/redirect/{provider}', ['uses' => 'User\UserController@redirectToProvider', 'as' => 'social.login']);
Route::get('social/callback/{provider}', 'User\UserController@handleProviderCallback');
//End Login/Signup


Route::get('/whatsapp/', function () {

    $user = new stdClass;
    $user->name = 'Benjamín Martínez Mateos';
    $user->phone = '5219512222222';

    $message = "Hello $user->name and welcome to our site";

    $messages = Whatsapi::send($message, function ($send) use ($user) {
        $send->to($user->phone);
    });

});


// Downloads

Route::group(['prefix' => 'download'], function () {
    Route::get('schedules', 'User\PdfController@print_schedules');
});


Route::group(['prefix' => 'download_pdf'], function () {
    Route::get('schedules', 'User\PdfController@print_schedules');
});