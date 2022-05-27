<?php

namespace App\Http\Controllers;

require "../vendor/autoload.php";

use Google_Client;
use Google_Service_Calendar;
use Illuminate\Support\Facades\Redirect;

class CalendarEventController extends Controller
{
    private static $scopeToBeRequested = "https://www.googleapis.com/auth/calendar.readonly";

    /**
     * Returns the currently set scope to be requested.
     * @return string|array
     */
    public static function getscopeToBeRequested() {
        return self::$scopeToBeRequested;
    }

    /**
     * Sets a new scope to be requested.
     */
    public static function setscopeToBeRequested($newScopeToBeRequested) {
        self::$scopeToBeRequested = $newScopeToBeRequested;
    }

    /**
     * Return a prepared Google_Client variable, ready for use.
     * @return Google_Client
     */
    public static function getGoogleClientObject(){
        $client = new Google_Client();
        $client->setDeveloperKey("AIzaSyDvOuqVFe350TPe2dzvK_F0ClOxa7d7Df4");
        $client->setScopes(self::getscopeToBeRequested());
        $client->setAuthConfig('../Storage/calendarAPI/credentials.json');
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');
        return $client;
    }

    /**
     * Requests a new authorization from user.
     */
    public static function requestNewAuthorization($request) {
        $client = self::getGoogleClientObject();
        $authCode = $request->code;
        $tokenPath = './Storage/calendarAPI/token.json';
        
        // Exchange authorization code for an access token.
        $accessToken = $client->fetchAccessTokenWithAuthCode($authCode); 
        $client->setAccessToken($accessToken);

        // Save the token to a file.
        if (!file_exists(dirname($tokenPath))) {
            mkdir(dirname($tokenPath), 0700, true);
        }
        file_put_contents($tokenPath, json_encode($client->getAccessToken()));        
    }

    /**
     * Returns an authorized API client.
     * @return Google_Client the authorized client object
     */
    public static function getClient() {
        
        $client = self::getGoogleClientObject();
        // Load previously authorized token from a file, if it exists.
        $tokenPath = '../Storage/calendarAPI/token.json';
        if (file_exists($tokenPath)) {
            $accessToken = json_decode(file_get_contents($tokenPath), true);
            $client->setAccessToken($accessToken);
        }

        // If there is no previous token or it's expired.
        if ($client->isAccessTokenExpired()) {
            // Refresh the token if possible, else fetch a new one.
            if ($client->getRefreshToken()) {
                $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            } else {
                // Request authorization from the user.
                $authenticationUrl = $client->createAuthUrl();
                // redirect user to site for authorization.                
                return Redirect::to($authenticationUrl);   
            }
            file_put_contents($tokenPath, json_encode($client->getAccessToken()));
        }
        return $client;
    }

    /**
     * Return an array of events from calendar.
     * @return array|null
     */
    public static function loadEventsFromCalendar() {

        // Get the API client and construct the service object.
        $googleClient = self::getGoogleClientObject();
        $googleClient->setAccessToken(self::getClient()->getAccessToken());
        $service = new Google_Service_Calendar($googleClient);        

        $optParams = array(
            'orderBy' => 'startTime',
            'singleEvents' => true,
        );

        $results = $service->events->listEvents('primary', $optParams);
        $events = $results->getItems();
        
        if (empty($events)) { 
            return null;  
        }          
        
        // Reverse array to show latest events first.
        return array_reverse($events);              
    }   
}
