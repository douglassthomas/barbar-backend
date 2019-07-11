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

Route::get('/', function () {
    return view('welcome');
});

Route::post('/login', 'ApiController@login');
Route::post('/register', 'ApiController@register');

Route::get('/tes', function (){
    return response()->json(
      [
          'tes'=>[
              ['name'=>'hiyaa'],
              ['satu'=>'dua']
          ]
      ]
    );
});



