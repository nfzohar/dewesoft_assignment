<?php

use Illuminate\Http\Request;
use App\Models\CalendarEvent;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\CalendarEventController;

// Main route.
Route::get('/', function () {

    return view('calendar_events', [
        'heading' => 'DeweSoft Project: employee assignment',
        'sub_heading' => 'Calendar Event List',
        'calendar_events' => CalendarEvent::loadEventList()
    ]);
});

// Redirect route for authentiation.
Route::get('/api', function (Request $request) {    
    if(!$request) {
        CalendarEventController::requestNewAuthorization($request);
    }   
    return Redirect::to("/");  
});
