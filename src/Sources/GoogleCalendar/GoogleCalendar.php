<?php

namespace ChrisHardie\CalendarCrawler\Sources\GoogleCalendar;

use ChrisHardie\CalendarCrawler\Exceptions\SourceNotCrawlable;
use ChrisHardie\CalendarCrawler\Models\CalendarSource;
use ChrisHardie\CalendarCrawler\Sources\BaseSource;
use Google_Client;
use Google_Service_Calendar;
use Illuminate\Support\Carbon;

class GoogleCalendar extends BaseSource
{
    public function getEvents(CalendarSource $source)
    {
        $client = $this->getClient();

        // Example: Print the next 10 events on the user's calendar.
        // https://developers.google.com/calendar/api/quickstart/php
        $calendarId = $source->location;
        $optParams = [
            'orderBy' => 'startTime',
            'singleEvents' => true,
            'timeMin' => date('c'),
            'timeMax' => Carbon::now()->addDays(30)->format('c'),
        ];
        $results = $client->events->listEvents($calendarId, $optParams);
        $events = $results->getItems();

        if (empty($events)) {
            throw new SourceNotCrawlable('No events found', 0, null, $source);
        } else {
            foreach ($events as $event) {
//                dd($event->start->getDateTime());
                $source->events()->updateOrCreate(
                    [
                        'source_internal_id' => $event->getICalUID(),
                    ],
                    [
                        'title' => $event->getSummary(),
                        'start_timestamp' => $event->start->getDateTime(),
                        'end_timestamp' => $event->end->getDateTime(),
                        'description' => $event->getDescription(),
                        'address' => $event->getLocation(),
                        'status' => $event->getStatus(),
                        'last_crawled_at' => Carbon::now(),
                    ]
                );
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
