<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes();

Route::get('/', 'HomeController@getIndex')->middleware('guest');
Route::get('/news', 'UserController@getNews');
Route::get('/ajax/register', 'HomeController@registerAjaxCheck');

Route::get('/preview/user/{userId}', 'UserController@getPreview');
Route::get('/preview/clan/{clanId}', 'ClanController@getPreview');

Route::prefix('/profile')->group(function() {
	Route::get('/index', 'ProfileController@getIndex');
	Route::post('/training', 'ProfileController@postTrainingUpdate');
	Route::get('/logo', 'ProfileController@getLogo');
	Route::post('/logo', 'ProfileController@postLogo');
	Route::get('/select/race', 'ProfileController@getRaceSelect');
	Route::post('/select/race', 'ProfileController@postRaceSelect');
	Route::get('/talents', 'ProfileController@getTalents');
	Route::post('/talents', 'ProfileController@postTalentsForm');
	Route::post('/talents/use', 'ProfileController@postTalentsUse');
	Route::post('/talents/reset/single', 'ProfileController@postTalentResetSingle');
});

Route::get('/hideout', 'UserController@getHideout');
Route::post('/hideout', 'UserController@postHideout');

Route::prefix('/city')->group(function() {
	Route::get('/index', 'CityController@getIndex');
	Route::get('/church', 'CityController@getChurch');
	Route::post('/church', 'CityController@postChurch');
	Route::get('/graveyard', 'CityController@getGraveyard');
	Route::post('/graveyard', 'CityController@postGraveyard');
	Route::post('/graveyard/cancel', 'CityController@postGraveyardCancel');
    Route::get('/shop', 'CityController@getShop');
    Route::post('/shop/item/buy/{itemId}', 'CityController@postShopItemBuy');
    Route::post('/shop/item/sell/{itemId}', 'CityController@postShopItemSell');
    Route::get('/library', 'CityController@getLibrary');
    Route::post('/library', 'CityController@postLibrary');
    Route::get('/taverne', 'CityController@getTaverne');
    Route::get('/missions', 'CityController@getMissions');
    Route::get('/missions/acceptMission/{missionId}', 'CityController@postAcceptMission');
    Route::get('/missions/finishMission/{missionId}', 'CityController@postFinishMission');
    Route::get('/missions/cancelMission/{missionId}', 'CityController@postCancelMission');
    Route::get('/missions/replaceOpenMissions', 'CityController@postReplaceOpenMissions');
    Route::get('/missions/replaceOpenMissionsForAp', 'CityController@postReplaceOpenMissionsForAp');
});

Route::prefix('/hunt')->group(function() {
	Route::get('/index', 'HuntController@getHunt');
	Route::get('/human/{huntId}', 'HuntController@postHumanHunt');
});

Route::get('/voodoo', 'CityController@getVoodoo');
Route::post('/voodoo', 'CityController@postVoodoo');

Route::prefix('/clan')->group(function() {
	Route::get('/index', 'ClanController@getIndex');
	Route::get('/create', 'ClanController@getCreate');
	Route::post('/create', 'ClanController@postCreate');
	Route::post('/newmessage', 'ClanController@postNewMessage');
	Route::get('/deletemessage', 'ClanController@postDeleteMessage');
	Route::post('/donate', 'ClanController@postDonate');
	Route::get('/hideout/upgrade', 'ClanController@postHideoutUpgrade');
	Route::get('/logo/background', 'ClanController@getLogoBackground');
	Route::get('/logo/symbol', 'ClanController@getLogoSymbol');
	Route::post('/logo/background', 'ClanController@postLogoBackground');
	Route::post('/logo/symbol', 'ClanController@postLogoSymbol');
	Route::get('/memberlist', 'ClanController@getMemberList');
	Route::get('/description', 'ClanController@getDescription');
	Route::post('/description', 'ClanController@postDescription');
	Route::get('/change/homepage', 'ClanController@getChangeHomepage');
	Route::get('/change/name', 'ClanController@getChangeName');
	Route::post('/change/homepage', 'ClanController@postChangeHomepage');
	Route::post('/change/name', 'ClanController@postChangeName');
	Route::get('/leave', 'ClanController@getLeave');
	Route::get('/clanleave', 'ClanController@postLeave');
	Route::get('/mail', 'ClanController@getClanMail');
	Route::post('/mail', 'ClanController@postClanMail');
	Route::get('/memberrights', 'ClanController@getMemberRights');
	Route::get('/view/homepage', 'ClanController@postVisitHomepage');
	Route::get('/memberlistExt/{clanId}', 'ClanController@getMemberListExternal');
});

Route::get('/notepad', 'UserController@getNotepad');
Route::post('/notepad', 'UserController@postNotepad');

Route::get('/settings', 'UserController@getSettings');

Route::get('/highscore', 'UserController@getHighscore');
Route::post('/highscore/mypos', 'UserController@postHighscoreMyPosition');

Route::get('/search', 'UserController@getSearch');
Route::post('/search', 'UserController@postSearch');

Route::get('/logout', function() {Auth::logout(); return redirect(url(''));});