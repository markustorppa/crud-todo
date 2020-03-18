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
    return view('frontpage', ['title' => 'Etusivu']);
})->name('frontpage');

Route::get('/todos', 'DashboardController@index')->name('todos');

Auth::routes();

Route::resource('/listapi/lists', 'ListController', [
  'except' => ['edit']
]);

Route::resource('/taskapi/tasks', 'TaskController', [
  'except' => ['edit', 'create', 'index', 'show']
]);

Route::resource('/userapi/user', 'UserController', [
  'except' => ['edit', 'create', 'show', 'update', 'destroy', 'store']
]);
