<?php

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

Auth::routes();
Route::group(['middleware' => 'langugue'], function() {

    Route::get('change-language/{language}', 'UserController@changeLanguage')->name('user.change-language');

    Route::get('/home', 'HomeController@index')->name('home');

    Route::get('/auth/redirect/{provider}', 'SocialController@redirect');

    Route::get('/callback/{provider}', 'SocialController@callback');

    Route::get('/complete-registration', 'Auth\RegisterController@completeRegistration');

    Route::post('/2fa', function () {
        return redirect(URL()->previous());
    })->name('2fa')->middleware('2fa'); 

    //prefit admin
    Route::prefix('admin')->group(function () 
    {
        Route::get('/manager/{num?}', 'UserController@index')       ->name('admin.manager');

        Route::get('export', 'CSVController@export')                ->name('admin.export');

        Route::post('/adduser', 'UserController@create')            ->name('admin.addUser');

        Route::get('/delete/{user}', 'UserController@delete')       ->name('admin.deleteuser');

        Route::post('/update', 'UserController@update')             ->name('admin.updateuser');

        Route::get('/noti/{id}', 'UserController@markRead')         ->name('demo.markread');

        Route::get('/all', 'UserController@markReadAll')            ->name('demo.xoahet');

        Route::get('/dispatch', 'UserController@queueAddUser')      ->name('demo.queueadduser');
    });
});