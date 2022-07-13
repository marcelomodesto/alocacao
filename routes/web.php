<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SchoolTermController;
use App\Http\Controllers\SchoolClassController;
use App\Http\Controllers\InstructorController;
use App\Http\Controllers\RoomController;

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
    return view('main');
});

Route::resource('schoolterms', SchoolTermController::class);

Route::get('/schoolclasses/search', [SchoolClassController::class, 'search'])->name('schoolclasses.search');
Route::get('/schoolclasses/import', [SchoolClassController::class, 'import'])->name('schoolclasses.import');
Route::resource('schoolclasses', SchoolClassController::class);

Route::resource('instructors', InstructorController::class);

Route::patch('/rooms/distributes', [RoomController::class, 'distributes'])->name('rooms.distributes');
Route::get('/rooms/dissociate/{schoolclass}', [RoomController::class, 'dissociate'])->name('rooms.dissociate');
Route::resource('rooms', RoomController::class);
