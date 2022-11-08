<?php

use App\Http\Controllers\ExploreController;
use App\Http\Controllers\TweetController;
use App\Http\Controllers\TweetLikesController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\MessagesController;
use Illuminate\Support\Facades\Route;

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

Route::resource('tweets', TweetController::class);
Route::get('users/{user:username}', [UserController::class, 'show'])
->middleware('auth')
->name('users.show');
Route::post('users/{user:username}/follow', [FollowController::class, 'store'])
->middleware('auth');
Route::get('users/{user:username}/edit', [userController::class, 'edit'])
->middleware('auth')
->name('users.edit');
Route::patch('users/{user:username}', [userController::class, 'update'])
->middleware('auth')
->name('users.update');
Route::get('/explore', [ExploreController::class, 'index']);
Route::post('tweets/{tweet}/like', [TweetLikesController::class, 'store']);
Route::delete('tweets/{tweet}/like', [TweetLikesController::class, 'destroy']);
// Route::group(['prefix' => 'messages'], function () {
//     Route::get('/', ['as' => 'messages', 'uses' => 'MessagesController@index']);
//     Route::get('create', ['as' => 'messages.create', 'uses' => 'MessagesController@create']);
//     Route::post('/', ['as' => 'messages.store', 'uses' => 'MessagesController@store']);
//     Route::get('{id}', ['as' => 'messages.show', 'uses' => 'MessagesController@show']);
//     Route::put('{id}', ['as' => 'messages.update', 'uses' => 'MessagesController@update']);
// });
Route::get('/messages', [MessagesController::class, 'index']);
Route::get('/messages/create', [MessagesController::class, 'create']);
Route::post('/messages/store', [MessagesController::class, 'store']);
Route::get('/messages/show', [MessagesController::class, 'show']);




require __DIR__.'/auth.php';
