<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\QuestionTypeController;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group(['middleware' => ['api']], function () {
    Route::post('login', 'AuthController@login')->name('login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');
        
    Route::group(['middleware' => ['admin']], function () {
        Route::group(['prefix' => 'question-type'], function () {
            Route::post('create', 'QuestionTypeController@create');
            Route::post('update/{id}', 'QuestionTypeController@update');
            Route::post('delete/{id}', 'QuestionTypeController@delete');
            Route::post('', 'QuestionTypeController@getAllQuestionType');
        });

        Route::group(['prefix' => 'question'], function () {
            Route::post('create', 'QuestionController@create');
            Route::post('update/{id}', 'QuestionController@update');
            Route::post('delete/{id}', 'QuestionController@delete');
            Route::post('', 'QuestionController@getAllQuestion');
        });

        Route::group(['prefix' => 'exam'], function () {
            Route::post('create', 'ExamController@create');
            Route::post('update/{id}', 'ExamController@update');
            Route::post('delete/{id}', 'ExamController@delete');
            Route::post('', 'ExamController@getAllExam');
            Route::post('publish', 'ExamController@publish');
        });
    });

    Route::group(['middleware' => ['user']], function () {
        Route::group(['prefix' => 'exam'], function () {
            Route::post('{id}', 'ExamController@getExamQuestion');
        });
    });
});