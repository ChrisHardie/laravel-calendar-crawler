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
    /**
     * Fetch a public Google calendar's events and create events from them.
     * https://developers.google.com/calendar/api/quickstart/php
     *
     * @param CalendarSource $source
     * @throws SourceNotCrawlable
     */
    public function getEvents(CalendarSource $source): void
    {
        $client = $this->getClient();

        $calendarId = $source->location;
        $optParams = [
            'orderBy' => 'startTime',
            'singleEvents' => true,
            'timeMin' => date('c'),
            'timeMax' => Carbon::now()->addDays(30)->format('c'),
        ];

        try {
            $results = $client->events->listEvents($calendarId, $optParams);
            $events = $results->getItems();
        } catch (\Exception $e) {
            throw new SourceNotCrawlable('Cannot fetch events from Google', 0, null, $source);
        }

        if (empty($events)) {
            throw new SourceNotCrawlable('No events found', 0, null, $source);
        } else {
            foreach ($events as $event) {
                $source->events()->updateOrCreate(
                    [
                        'source_internal_id' => $event->getICalUID(),
                    ],
                    [
                        'title' => $event->getSummary(),
                        'start_timestamp' => Carbon::createFromFormat('c', $event->start->getDateTime())
                            ->setTimezone('UTC'),
                        'end_timestamp' => Carbon::createFromFormat('c', $event->end->getDateTime())
                            ->setTimezone('UTC'),
                        'description' => $event->getDescription(),
                        'url' => $event->getHtmlLink(),
                        'address' => $event->getLocation(),
                        'status' => $event->getStatus(),
                        'last_crawled_at' => Carbon::now(),
                    ]
                );
            }
        }
    }

    /**
     * Instantiate a Google Calendar Service API client
     * @return Google_Service_Calendar
     */
    private function getClient(): Google_Service_Calendar
    {
        $client = new Google_Client();
        $client->setApplicationName("Client_Library_Examples");
        $client->setDeveloperKey(config('calendar-crawler.auth.google.api_key'));

        return new Google_Service_Calendar($client);
    }
}
