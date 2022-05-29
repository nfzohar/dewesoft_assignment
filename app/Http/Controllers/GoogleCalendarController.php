<?php

namespace App\Http\Controllers;

use DateTime;
use App\Models\GoogleCalendar;
use Illuminate\Support\Facades\DB;

class GoogleCalendarController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $events = self::getResources();
        
        /*$eventsList = [];

        foreach ($events as $event) {
            array_push($eventsList,
            [
                'title' => $event['summary'],
                'description' => $event['description'],
                'start_date' => $event['start']['dateTime']
            ]);
        }*/

        return view('calendar_events', [
            'calendar_events' => []
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
     * Compares data from APi and data old data from the database.
     * Deletes data from database, not present in the API.
     *
     * @return \Illuminate\Http\Response
     */
    public static function deleteOldFromDatabase() {
        // Get the authorized client object and fetch the resources.
        $client = GoogleCalendar::oauth();
        // Get new event list from the API.
        $newEventsList = GoogleCalendar::getResource($client);
        // Get old event list from database.
        $eventsListFromDatabase = self::getFromDatabase();

        foreach ($eventsListFromDatabase as $eventFromDatabase) {
            if(!(in_array($eventFromDatabase, $newEventsList))) {
                DB::table('calendar_events')->delete($eventFromDatabase->id);
            }
        }     
    }


    /**
     * Get list of events from Calendar, write them to database.
     *
     * @return \Illuminate\Http\Response
     */
    public static function writeToDatabase() {
        // Get the authorized client object and fetch the resources.
        $client = GoogleCalendar::oauth();
        $eventsList = GoogleCalendar::getResource($client);

        foreach ($eventsList as $eventsListing) {
            // Check if the listing is already in the database.
            $existsIneDatabase = DB::table('calendar_events')
            ->where('event_title', '=', $eventsListing['summary'])
            ->where('event_start_time', '=', $eventsListing['start']['dateTime'])
            ->where('event_description', '=', $eventsListing['description'])
            ->first();

            if(empty($existsIneDatabase)){
                DB::table('calendar_events')->insertGetId(
                    array(
                        'event_title' => $eventsListing['summary'],
                        'event_start_time' => new DateTime($eventsListing['start']['dateTime']),
                        'event_description' => $eventsListing['description']
                    )
                );
            }
        }
    }

    /**
     * Get event list from database.
     *
     * @return \Illuminate\Http\Response
     */
    public static function getFromDatabase() {
        $eventsList = [];

        // Build the SELECT query.
        $listFromDatabase = DB::table('calendar_events')
        ->select('id', 'event_title', 'event_start_time', 'event_description')
        ->orderBy('event_start_time', 'desc')->get(); 

        foreach ($listFromDatabase as $listingFromDatabase) {
            // Format the date to [00 Month Year].
            $listingFromDatabase->event_start_time = 
            date_format(new DateTime($listingFromDatabase->event_start_time), 'd F o');
 
            array_push($eventsList, $listingFromDatabase);
        }

        return $eventsList;
    }


    /**
     * Get list of events from Calendar, write them to database.
     *
     * @return \Illuminate\Http\Response
     */
    public static function getResources() {

        // Delete old data from the database.
        self::deleteOldFromDatabase();
        // Write new data to database.
        self::writeToDatabase();
        // Get updated data from database.
        $eventsListFromDatabase = self::getFromDatabase();

        return response()->json([
            'status' => true,
            'events' => $eventsListFromDatabase
        ]);
    }

}