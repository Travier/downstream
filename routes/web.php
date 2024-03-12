<?php

use App\Http\Controllers\CollectionController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Route;

Auth::routes();

Route::middleware(['admin'])->group(function () {

    // Log Viewer
    Route::get('/admin/logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');

    //GlobalQueue
    Route::get('/admin/globalqueue', 'Admin\GlobalQueueController@getLatestMedia');

    Route::get('/admin/test/player', "AdminController@getTestPlayer");
    Route::get('/admin/engine', 'AdminController@getEngineFeed');
    Route::get('/admin/dash', 'AdminController@index');
    Route::post('/admin/dash/settings', 'AdminController@postServerSettings');

    Route::get('/admin/engine/clean', 'AdminController@getEngineClean');
    Route::get('/admin/media/edit', 'AdminController@getEngineClean');

    Route::get('/admin/filter/title', 'FilterController@getTitle');
    Route::post('/admin/filter/title', 'FilterController@postTitle');

    Route::get('/admin/user/list', 'Admin\UserListController@getList');

    Route::get('/admin/toplist', 'Admin\ToplistController@getIndex');
    Route::get('/admin/toplist/update', 'Admin\ToplistController@getUpdate');
    Route::get('/admin/toplist/clear', 'Admin\ToplistController@getClear');
    Route::get('/admin/toplist/by/{sort}', 'Admin\ToplistController@getIndex');
    Route::get('/admin/toplist/media/visible/{tempItemId}/{isVisible}', 'Admin\ToplistController@getMediaTempVisible');
});

Route::get('/user/list', 'UserController@getUserList');

Route::get('/search', 'SearchController@getIndex');
//Route::get('/search', 'SearchController@postSearchYouTube');

Route::get('/', 'FrontPageController@getLanding');
Route::get('/all', 'FrontPageController@index');
Route::get('/global', 'FrontPageController@getGlobalPage');

//Artist
Route::get('/artists', "ArtistController@getIndex");

// Collection
Route::get('/collection', [CollectionController::class, 'getIndex']);
Route::get('/collection/slice', [CollectionController::class, 'getSlice']);


//Link Sharing
//View a media item
Route::get('/v/{index}', 'LinkController@viewMediaItem');
//Backward compat for old links
Route::get('/media/{index}', 'LinkController@viewMediaItem');

//Import
Route::get('/import', 'ImportController@getIndex');
Route::post('/import', 'ImportController@postImportVideo');

Route::get('/user', 'UserController@getIndex');
Route::get('/test/video', 'MediaAPIController@testVideo');

//User
Route::get('/guide', 'UserController@getGuidePage');
Route::get('/settings', 'UserSettingController@getSettingsPage');
Route::post('/settings', 'UserSettingController@postSettings');
Route::get('/spotify/connect', 'UserController@getConnect');
Route::get('/spotify/import', 'UserController@getSpotifyImportPage');

//Feed
Route::get('/feed', 'FeedController@index');

//Privacy
Route::get('/privacy', function () {
    return View::make("privacy");
});

//Privacy
Route::get('/tos', function () {
    return View::make("tos");
});

//Privacy
Route::get('/contact', function () {
    return View::make("contact");
});

Route::get('/logout', 'UserController@logout');

Route::get('/{vue_capture?}', function () {
    return view('index');
})->where('vue_capture', '[\/\w\.-]*');
