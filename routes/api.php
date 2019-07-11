<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});



Route::group(['middleware' => 'auth.jwt'], function () {



});

Route::get('logout', 'ApiController@logout');

Route::get('user', 'ApiController@getAuthUser');
Route::get('products', 'ProductController@index');
Route::get('products/{id}', 'ProductController@show');
Route::post('products', 'ProductController@store');
Route::put('products/{id}', 'ProductController@update');
Route::delete('products/{id}', 'ProductController@destroy');

Route::middleware('jwt.auth')->group(function (){
    Route::post('getUserId', 'ApiController@getUserId');
    Route::post('editProfile', 'ApiController@editProfile');

    Route::post('test', function (Request $request){
        return response()->json([
            'status'=>true
        ]);
    });

    Route::post('logout', 'ApiController@logout');

    Route::post('addHouse', 'ApiController@addHouse');
    Route::post('addApartement', 'ApiController@addApartement');

    Route::post('updateHouse', 'ApiController@updateHouse');

    Route::post('goChangePassword', 'ApiController@goChangePassword');
    Route::post('goEditProfileInfo', 'ApiController@goEditProfileInfo');

    Route::post('addImageToUser', 'PostController@addImageToUser');
    Route::post('getMyImages', 'PostController@getMyImages');

    Route::post('goPost', 'PostController@goPost');
    Route::post('getMyPost', 'PostController@getMyPost');
    Route::post('deletePost', 'PostController@deletePost');

    Route::post('addFavorite', 'FavoriteController@addFavorite');
    Route::post('getFavoriteByUserId', 'FavoriteController@getFavoriteByUserId');
    Route::post('deleteFavorite', 'FavoriteController@deleteFavorite');
    Route::post('checkFavorite', 'FavoriteController@checkFavorite');

    Route::post('addHistory', 'HistoryController@addHistory');
    Route::post('getHistoryByUserId', 'HistoryController@getHistoryByUserId');

    Route::post('insertPremium', 'PremiumController@insertPremium');
    Route::post('deletePremium', 'PremiumController@deletePremium');
    Route::post('addPromo', 'PremiumController@addPromo');
    Route::post('deletePromo', 'PremiumController@deletePromo');

    Route::post('getUserPremiumDate', 'PremiumController@getUserPremiumDate');
    Route::post('checkout', 'PremiumController@checkout');

    Route::post('getPremiumTransaction', 'PremiumTransactionController@getPremiumTransaction');
    Route::post('getAllUser', 'UserController@getAllUser');

    Route::post('banUser', 'UserController@banUser');
    Route::post('deleteUser', 'UserController@deleteUser');
    Route::post('resetPasswordUser', 'UserController@resetPasswordUser');

    Route::post('goReport', 'ReportController@goReport');
    Route::post('getAllReport', 'ReportController@getAllReport');


    Route::post('getMyPremium', 'PremiumController@getMyPremium');

    Route::post('delProperty', 'ApiController@delProperty');

    Route::post('checkFollow', 'ApiController@checkFollow');
    Route::post('follow', 'ApiController@follow');
    Route::post('unfollow', 'ApiController@unfollow');
});

Route::post('getPremium', 'PremiumController@getPremium');


Route::post('getAllPost', 'PostController@getAllPost');
Route::post('searchPost', 'PostController@search');
Route::post('getPostById', 'PostController@getPostById');

Route::get('getUserInfo', 'ApiController@getUserInfo');
Route::get('getAdminDashboardInfo', 'ApiController@getAdminDashboardInfo');

Route::get('getAllProperty', 'ApiController@getAll');
Route::get('incrementView', 'ApiController@incrementView');
Route::post('insertReview', 'ApiController@insertReview');
Route::post('insertRating', 'ApiController@insertRating');
Route::post('getReviewByPropertyId', 'ApiController@getReviewByPropertyId');
Route::post('getRatingAverage', 'ApiController@getRatingAverage');
Route::post('getReviewByPropertyIdnopage', 'ApiController@getReviewByPropertyIdnopage');

Route::get('/sendEmail', 'MailController@send');
Route::post('verifyEmail', 'VerifyEmailController@verifyUser');



Route::get('getCity', 'ApiController@getCity');

Route::post('getOwnerData', 'ApiController@getOwnerData');
Route::post('getFollowing', 'ApiController@getFollowing');

Route::post('register', 'ApiController@register');
Route::post('login', 'ApiController@login');
Route::post('getAuthUser', 'ApiController@getAuthUser');

Route::post('testRedis', 'ApiController@testRedis');
Route::post('getChatList', 'ApiController@getChatList');
Route::post('getAllChat', 'ApiController@getAllChat');
Route::post('sendChat', 'ApiController@sendChat');
Route::post('getRead', 'ApiController@getRead');
Route::post('read', 'ApiController@read');

Route::post('getKostByOwnerId', 'ApiController@getKostByOwnerId');
Route::post('getApartementByOwnerId', 'ApiController@getApartementByOwnerId');

Route::get('getPropertyById', 'ApiController@getPropertyById');

Route::post('insertPublicFacilities', 'PublicFacilitiesController@insert');
Route::post('insertRoomFacilities', 'RoomFacilitiesController@insert');
Route::get('getRoomFacilities', 'RoomFacilitiesController@get');
Route::get('getRoomFacilitiesPaginate', 'RoomFacilitiesController@getPaginate');
Route::get('getPublicFacilities', 'PublicFacilitiesController@get');
Route::get('getPublicFacilitiesPaginate', 'PublicFacilitiesController@getPaginate');
Route::post('deleteRoomFacility', 'RoomFacilitiesController@delete');
Route::post('deletePublicFacility', 'PublicFacilitiesController@delete');
Route::post('updatePublicFacilities', 'PublicFacilitiesController@update');
Route::post('updateRoomFacilities', 'RoomFacilitiesController@update');

Route::post('search', 'ApiController@search');
Route::post('coba', 'ApiController@cobaReview');
