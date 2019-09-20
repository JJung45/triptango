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

Route::get('/', 'MainController@index');

Route::get('quiz/ajax_index','QuizController@ajax_index');
Route::resource('/quiz', 'QuizController');

Route::resource('/result', 'ResultController');

Route::get('/auth/redirect/{provier}', 'SocialController@redirect');

Route::get('/callback/{provier}', 'SocialController@callback');

Route::get('/logout','SocialController@logout');
