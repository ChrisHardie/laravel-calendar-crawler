<?php

namespace ChrisHardie\CalendarCrawler\Sources\GoogleCalendar;

use ChrisHardie\CalendarCrawler\Sources\BaseSource;
use ChrisHardie\CalendarCrawler\Models\CalendarSource;
use Google_Client;
use Google_Service_Calendar;

class GoogleCalendar extends BaseSource
{
    public function getEvents(CalendarSource $source): \Illuminate\Database\Eloquent\Collection
    {
        $client = $this->getClient();

        // Example: Print the next 10 events on the user's calendar.
        // https://developers.google.com/calendar/api/quickstart/php
        $calendarId = $source->location;
        $optParams = array(
            'maxResults' => 10,
            'orderBy' => 'startTime',
            'singleEvents' => true,
            'timeMin' => date('c'),
        );
        $results = $client->events->listEvents($calendarId, $optParams);
        $events = $results->getItems();

        if (empty($events)) {
            print "No upcoming events found.\n";
        } else {
            print "Upcoming events:\n";
            foreach ($events as $event) {
                $start = $event->start->dateTime;
                if (empty($start)) {
                    $start = $event->start->date;
                }
                printf("%s (%s)\n", $event->getSummary(), $start);
            }
        }
    }


    private function getClient()
    {
        $client = new Google_Client();
        $client->setApplicationName("Client_Library_Examples");
        $client->setDeveloperKey(config('calendar-crawler.auth.google.api_key'));
        return new Google_Service_Calendar($client);
    }
}
