<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CourseClassController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\ClothingController;
use App\Http\Controllers\PartnerTrainingUserController;
use App\Http\Controllers\Auth\LoginController;

//Main Page
Route::get('/', function () {
    return view('master.main');
})->name('master.main')->middleware('auth');

//Login
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');


//Tecnico & Admin
Route::middleware(['auth', 'checkRole:admin, tecnico'])->group(function () {
    //Route::get('/home', 'HomeController@index')->name('home');
    //Route::resource('students', 'StudentController');

    Route::resource('users', 'UserController');
    Route::post('users/massDelete', 'UserController@massDelete')->name('users.massDelete');

    Route::resource('materials', 'MaterialController');
    Route::post('materials/massDelete', 'MaterialController@massDelete')->name('materials.massDelete');

    Route::resource('trainings', 'TrainingController');
    Route::post('trainings/massDelete', 'TrainingController@massDelete')->name('trainings.massDelete');

    Route::resource('clothing', 'ClothingController');

    Route::resource('clothing-assignment', 'ClothingAssignmentController');
    Route::get('/clothing-assignment/users/{id}', 'ClothingAssignmentController@index')->name('clothing-assignment.users');

    Route::get('/material-clothing-delivery/create/{id}', 'MaterialClothingDeliveryController@create')->name('material-clothing-delivery.create');
    Route::post('/material-clothing-delivery', 'MaterialClothingDeliveryController@store')->name('material-clothing-delivery.store');

    Route::resource('external', 'PartnerTrainingUserController');
    Route::post('external/massDelete', 'PartnerTrainingUserController@massDelete')->name('external.massDelete');

    Route::resource('partners', 'PartnerController');
    Route::delete('partner-contact/{partner_contact}', 'PartnerContactController@destroy')->name('partner-contact.destroy');
    Route::post('partners/massDelete', 'PartnerController@massDelete')->name('partners.massDelete');

    Route::resource('course-classes', 'CourseClassController');
    Route::post('course-classes/massDelete', 'CourseClassController@massDelete')->name('course-classes.massDelete');

    Route::resource('courses', 'CourseController');
    Route::post('courses/massDelete', 'CourseController@massDelete')->name('courses.massDelete');
    Route::resource('tickets', 'TicketController');
});

Route::middleware(['auth', 'checkRole:user'])->group(function () {
});
