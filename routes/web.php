<?php

use Illuminate\Support\Facades\Route;

// Main route
Route::get('/calendar-event-list', 'App\Http\Controllers\GoogleCalendarController@index');

Route::get('/', 'App\Http\Controllers\GoogleCalendarController@getResources');

Route::get('/api', 'App\Http\Controllers\GoogleCalendarController@store');

Route::get('/', 'App\Http\Controllers\GoogleCalendarController@connect');

Route::get('/api', 'App\Http\Controllers\GoogleCalendarController@store');