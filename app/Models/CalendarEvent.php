<?php

namespace App\Models;

use App\Http\Controllers\CalendarEventController;
use DateTime;

class CalendarEvent
{

    /**
     * Shortens text to desired lenght. Adds ... if text is longer than desired.
     * @return string
     */
    public static function shortenText($textToBeShortened, $desiredOutputLenght) {
        $output = substr($textToBeShortened, 0, $desiredOutputLenght);

        if(strlen($textToBeShortened) > $desiredOutputLenght) {
            $output .= '...';
        }
        return $output;
    }


    /**
     * Formats a DateTime/String variable and returns a formatted string value.
     * @return string
     */
    public static function formattDateTime($dateToBeFormatted, $desiredDateFormat) {
        return date_format(new DateTime($dateToBeFormatted), $desiredDateFormat);
    }
    
    /**
    * Returns a formatted array of event data to display.
    * 
    * @return array
    */
    public static function loadEventList() {
        $googleClient = CalendarEventController::getGoogleClientObject();
        $formattedEvennList = [];

        if(!$googleClient->getAccessToken()) {
            $unformatterEventList = CalendarEventController::loadEventsFromCalendar();
        } else {
            CalendarEventController::getClient();
        }
        
        if(empty($unformatterEventList)) {
            return null;
        }

        // Extract and format the data to be displayed from the array of all event data.
        foreach ($unformatterEventList as $unformatterEvent) {
            array_push($formattedEvennList,
            [
                'title' => $unformatterEvent['summary'],
                'description' => self::shortenText($unformatterEvent['description'], 80),
                'start_date' => self::formattDateTime($unformatterEvent['start']['dateTime'], 'd. F o')
            ] );    
        }

        return $formattedEvennList;
    }

}
    