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

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

// Login & logout function
Route::get('/login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('/login', 'Auth\LoginController@login');
Route::post('/logout', 'Auth\LoginController@logout')->name('logout');

// Password Reset Routes
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.update');

Route::middleware('auth')->group(function () {

    //Campus
    Route::get('campus', 'CampusController@index')->name('campus');
    Route::get('campus/view/{id}', 'CampusController@show')->name('campus.show');
    Route::get('/campus/search', 'CampusController@search')->name('campus.search');

    //Tahun
    Route::get('tahun', 'TahunController@index')->name('tahun');
    Route::get('tahun/view/{id}', 'TahunController@show')->name('tahun.show');
    Route::get('/tahun/search', 'TahunController@search')->name('tahun.search');

    //Department
    Route::get('department', 'DepartmentController@index')->name('department');
    Route::get('department/view/{id}', 'DepartmentController@show')->name('department.show');
    Route::get('/department/search', 'DepartmentController@search')->name('department.search');

    //Sub Unit
    Route::get('subunit', 'SubUnitController@index')->name('subunit');
    Route::get('subunit/view/{id}', 'SubUnitController@show')->name('subunit.show');
    Route::get('/subunit/search', 'SubUnitController@search')->name('subunit.search');

    //Jenis Data PTJ
    Route::get('jenisdataptj', 'JenisDataPtjController@index')->name('jenisdataptj');
    Route::get('jenisdataptj/view/{id}', 'JenisDataPtjController@show')->name('jenisdataptj.show');
    Route::get('/jenisdataptj/search', 'JenisDataPtjController@search')->name('jenisdataptj.search');

    //Position
    Route::get('position', 'PositionController@index')->name('position');
    Route::get('position/view/{id}', 'PositionController@show')->name('position.show');
    Route::get('/position/search', 'PositionController@search')->name('position.search');


    Route::get('/home', 'HomeController@index')->name('home');

    // User Profile
    Route::get('profile/{id}', 'UserProfileController@show')->name('profile.show');
    Route::get('profile/{id}/edit', 'UserProfileController@edit')->name('profile.edit');
    Route::put('profile/{id}', 'UserProfileController@update')->name('profile.update');
    Route::get('profile/{id}/change-password', 'UserProfileController@changePasswordForm')->name('profile.change-password');
    Route::post('profile/{id}/change-password', 'UserProfileController@changePassword')->name('profile.update-password');

    // Superadmin - Activity Log
    Route::get('activity-log', 'ActivityLogController@index')->name('activity-log');
    Route::get('/debug-logs', 'ActivityLogController@showDebugLogs')->name('logs.debug');

    // User Management
    Route::get('user', 'UserController@index')->name('user');
    Route::get('user/create', 'UserController@create')->name('user.create');
    Route::post('user/store', 'UserController@store')->name('user.store');
    Route::get('user/{id}/edit', 'UserController@edit')->name('user.edit');
    Route::post('user/{id}', 'UserController@update')->name('user.update');
    Route::get('user/view/{id}', 'UserController@show')->name('user.show');
    Route::get('/user/search', 'UserController@search')->name('user.search');
    Route::delete('user/{id}', 'UserController@destroy')->name('user.destroy');
    Route::get('/user/trash', 'UserController@trashList')->name('user.trash');
    Route::get('/user/{id}/restore', 'UserController@restore')->name('user.restore');
    Route::delete('/user/{id}/force-delete', 'UserController@forceDelete')->name('user.forceDelete');

    // User Role Management
    Route::get('user-role', 'UserRoleController@index')->name('user-role');
    Route::get('user-role/create', 'UserRoleController@create')->name('user-role.create');
    Route::post('user-role/store', 'UserRoleController@store')->name('user-role.store');
    Route::get('user-role/{id}/edit', 'UserRoleController@edit')->name('user-role.edit');
    Route::post('user-role/{id}', 'UserRoleController@update')->name('user-role.update');
    Route::get('user-role/view/{id}', 'UserRoleController@show')->name('user-role.show');
    Route::get('/user-role/search', 'UserRoleController@search')->name('user-role.search');
    Route::delete('user-role/{id}', 'UserRoleController@destroy')->name('user-role.destroy');
    Route::get('/user-role/trash', 'UserRoleController@trashList')->name('user-role.trash');
    Route::get('/user-role/{id}/restore', 'UserRoleController@restore')->name('user-role.restore');
    Route::delete('/user-role/{id}/force-delete', 'UserRoleController@forceDelete')->name('user-role.forceDelete');

    //Campus
    Route::get('campus/create', 'CampusController@create')->name('campus.create');
    Route::post('campus/store', 'CampusController@store')->name('campus.store');
    Route::get('campus/{id}/edit', 'CampusController@edit')->name('campus.edit');
    Route::post('campus/{id}', 'CampusController@update')->name('campus.update');
    Route::delete('campus/{id}', 'CampusController@destroy')->name('campus.destroy');
    Route::get('/campus/trash', 'CampusController@trashList')->name('campus.trash');
    Route::get('/campus/{id}/restore', 'CampusController@restore')->name('campus.restore');
    Route::delete('/campus/{id}/force-delete', 'CampusController@forceDelete')->name('campus.forceDelete');

    //Tahun
    Route::get('tahun/create', 'TahunController@create')->name('tahun.create');
    Route::post('tahun/store', 'TahunController@store')->name('tahun.store');
    Route::get('tahun/{id}/edit', 'TahunController@edit')->name('tahun.edit');
    Route::post('tahun/{id}', 'TahunController@update')->name('tahun.update');
    Route::delete('tahun/{id}', 'TahunController@destroy')->name('tahun.destroy');
    Route::get('/tahun/trash', 'TahunController@trashList')->name('tahun.trash');
    Route::get('/tahun/{id}/restore', 'TahunController@restore')->name('tahun.restore');
    Route::delete('/tahun/{id}/force-delete', 'TahunController@forceDelete')->name('tahun.forceDelete');

    //Department
    Route::get('department/create', 'DepartmentController@create')->name('department.create');
    Route::post('department/store', 'DepartmentController@store')->name('department.store');
    Route::get('department/{id}/edit', 'DepartmentController@edit')->name('department.edit');
    Route::post('department/{id}', 'DepartmentController@update')->name('department.update');
    Route::delete('department/{id}', 'DepartmentController@destroy')->name('department.destroy');
    Route::get('/department/trash', 'DepartmentController@trashList')->name('department.trash');
    Route::get('/department/{id}/restore', 'DepartmentController@restore')->name('department.restore');
    Route::delete('/department/{id}/force-delete', 'DepartmentController@forceDelete')->name('department.forceDelete');

    //Sub Unit
    Route::get('subunit/create', 'SubUnitController@create')->name('subunit.create');
    Route::post('subunit/store', 'SubUnitController@store')->name('subunit.store');
    Route::get('subunit/{id}/edit', 'SubUnitController@edit')->name('subunit.edit');
    Route::post('subunit/{id}', 'SubUnitController@update')->name('subunit.update');
    Route::delete('subunit/{id}', 'SubUnitController@destroy')->name('subunit.destroy');
    Route::get('/subunit/trash', 'SubUnitController@trashList')->name('subunit.trash');
    Route::get('/subunit/{id}/restore', 'SubUnitController@restore')->name('subunit.restore');
    Route::delete('/subunit/{id}/force-delete', 'SubUnitController@forceDelete')->name('subunit.forceDelete');

    //Jenis Data PTJ
    Route::get('jenisdataptj/create', 'JenisDataPtjController@create')->name('jenisdataptj.create');
    Route::post('jenisdataptj/store', 'JenisDataPtjController@store')->name('jenisdataptj.store');
    Route::get('jenisdataptj/{id}/edit', 'JenisDataPtjController@edit')->name('jenisdataptj.edit');
    Route::post('jenisdataptj/{id}', 'JenisDataPtjController@update')->name('jenisdataptj.update');
    Route::delete('jenisdataptj/{id}', 'JenisDataPtjController@destroy')->name('jenisdataptj.destroy');
    Route::get('/jenisdataptj/trash', 'JenisDataPtjController@trashList')->name('jenisdataptj.trash');
    Route::get('/jenisdataptj/{id}/restore', 'JenisDataPtjController@restore')->name('jenisdataptj.restore');
    Route::delete('/jenisdataptj/{id}/force-delete', 'JenisDataPtjController@forceDelete')->name('jenisdataptj.forceDelete');
    Route::get('/get-subunits/{department_id}', 'JenisDataPtjController@getSubunits')->name('jenisdataptj.getSubunits');

    //Position
    Route::get('position/create', 'PositionController@create')->name('position.create');
    Route::post('position/store', 'PositionController@store')->name('position.store');
    Route::get('position/{id}/edit', 'PositionController@edit')->name('position.edit');
    Route::post('position/{id}', 'PositionController@update')->name('position.update');
    Route::delete('position/{id}', 'PositionController@destroy')->name('position.destroy');
    Route::get('/position/trash', 'PositionController@trashList')->name('position.trash');
    Route::get('/position/{id}/restore', 'PositionController@restore')->name('position.restore');
    Route::delete('/position/{id}/force-delete', 'PositionController@forceDelete')->name('position.forceDelete');
});
