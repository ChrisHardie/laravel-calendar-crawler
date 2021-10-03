<?php

namespace ChrisHardie\CalendarCrawler\Sources\GoogleCalendar;

use ChrisHardie\CalendarCrawler\Sources\BaseSource;
use ChrisHardie\CalendarCrawler\Models\CalendarSource;

class GoogleCalendar extends BaseSource
{
    public function getEvents(CalendarSource $source): \Illuminate\Database\Eloquent\Collection
    {
        // TODO: Implement getEvents() method.
    }
}
