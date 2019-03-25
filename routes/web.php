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

// Load Url rewrite
if ( env('PROXY_PASS') == true )
    URL::forceRootUrl(env('PROXY_PASS_URL'));

// Authentication Routes...
Route::get('/login', 'Auth\LoginController@showLoginFrom')->name('login_page');
Route::post('/login', 'Auth\LoginController@login') ->name('login');

Route::post('/logout', 'Auth\LoginController@logout')->name('logout')->middleware('auth');
Route::get('/logout', 'Auth\LoginController@logout')->name('logout')->middleware('auth');

Route::get('/register', 'Auth\RegisterController@show')->name('register_page');
Route::post('/register', 'Auth\RegisterController@register')->name('register');

// Show
Route::get('/', 'HomeController@index')->name('home');
Route::resource('/student', 'StudentController')->only(['index', 'show']);
Route::resource('/contest', "ContestController")->only(['index', 'show']);

// Admin
Route::prefix('/admin/')->namespace('Admin')->middleware(['auth'])->name('admin.')->group(function () {

    Route::get('/', function() {
        return redirect()->route('admin.contest.create');
    });

    Route::get('index', function (){
        return redirect()->route('admin.contest.create');
    })->name('index');

    Route::resource('contest', "ContestController")->except(['update', 'edit']);
    Route::resource('student', "StudentController")->except(['create', 'edit', 'show']);
    Route::resource('user', "UserController")->except(['create', 'edit', 'show']);
    Route::resource('group', "GroupController")->except(['create', 'edit', 'show', 'update']);

    Route::post('check/new/student', 'SubmitCheckController@student')->name('check_student');
    Route::post('list/students', 'GetListController@studentByGroup')->name('get.student.list.by.group');

});

Route::get('/teapot', function () {
    \App\Log::info('some one found a teapot');
    abort(418);
})->name('teapot');
