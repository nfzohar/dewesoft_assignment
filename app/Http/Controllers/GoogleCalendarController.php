<?php

namespace App\Http\Controllers;

use App\Models\GoogleCalendar;


class GoogleCalendarController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $events = self::getResources();
        $eventsList = [];

        //dd($events);

        foreach ($events as $event) {
            array_push($eventsList,
            [
                'title' => $event['summary'],
                'description' => $event['description'],
                'start_date' => $event['start']['dateTime']
            ]);
        }

        return view('calendar_events', [
            'calendar_events' => $eventsList
        ]);
    }

    /**
     * Redirects to request authentication from the user.
     */
    public static function connect() {
        $client = GoogleCalendar::getClient();
        $authUrl = $client->createAuthUrl();
        return redirect($authUrl);
    }

    /**
     * Generates and stores the accessTokens to the client_secret_generated.json file.
     */
    public static function store() {
        $client = GoogleCalendar::getClient();
        $authCode = request('code');
        $credentialsPath = storage_path('calendarAPI/client_secret_generated.json');
        // Exchange authorization code for an access token.
        $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);

        // Store the credentials to disk.
        if (!file_exists(dirname($credentialsPath))) {
            mkdir(dirname($credentialsPath), 0700, true);
        }
        file_put_contents($credentialsPath, json_encode($accessToken));
        return redirect('/calendar-event-list')->with('message', 'Credentials saved');
    }

    /**
     * Fetch the resources from google calendar.
     */
    public static function getResources() {
        // Get the authorized client object and fetch the resources.
        $client = GoogleCalendar::oauth();
        return GoogleCalendar::getResource($client);
    }

}
