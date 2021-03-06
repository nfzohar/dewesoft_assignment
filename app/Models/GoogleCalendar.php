<?php

namespace App\Models;

use Google_Client;
use Google_Service_Calendar;

class GoogleCalendar 
{
    // 
    private static $scopeToBeRequested = "https://www.googleapis.com/auth/calendar.readonly";

    /**
     * Returns the currently set scope to be requested.
     * @return string|array
     */
    public static function getscopeToBeRequested()
    {
        return self::$scopeToBeRequested;
    }

    /**
     * Sets a new scope to be requested.
     */
    public static function setscopeToBeRequested($newScopeToBeRequested)
    {
        self::$scopeToBeRequested = $newScopeToBeRequested;
    }

    /*
     * Create getClient function. 
     */ 	
	public static function getClient()
    {
        $client = new Google_Client();
        $client->setScopes(self::getscopeToBeRequested());
        $client->setAuthConfig(storage_path('calendarAPI/client_secret.json'));
        $client->setAccessType('offline');
        return $client;
    }

    /**
     * Returns an authorized API client.
     * @return Google_Client the authorized client object
     */
    public static function oauth() {
        
        $client = self::getClient();

        // Load previously authorized credentials from a file.
        $credentialsPath = storage_path('calendarAPI/client_secret_generated.json');
        if (!file_exists($credentialsPath)) {
            return false;
        }

        $accessToken = json_decode(file_get_contents($credentialsPath), true);
        $client->setAccessToken($accessToken);

        // Refresh the token if it's expired.
        if ($client->isAccessTokenExpired()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            file_put_contents($credentialsPath, json_encode($client->getAccessToken()));
        }

        return $client;
    }

    /**
     * Loads previously authorized token from a file, if it exists. The 
     * file client_secret_generated.json stores the user’s access and 
     * refresh tokens, and is created automatically when the 
     * authorization flow completes for the first time. 
     */						
    public static function getResource($client) {
        //$client = self::getClient();
        $service = new Google_Service_Calendar($client);;

        // On the user's calenda print the next 10 events .
        $calendarId = 'primary';
        $optParams = array(
        'orderBy' => 'startTime',
        'singleEvents' => true,
        );

        $results = $service->events->listEvents($calendarId, $optParams);
        //dd($results->getItems());        
        return $results->getItems();
    }
}
