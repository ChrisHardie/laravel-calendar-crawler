<?php

namespace ChrisHardie\CalendarCrawler\Http;

use Spatie\IcalendarGenerator\Components\Calendar;
use Spatie\IcalendarGenerator\Properties\TextProperty;

class CalendarController
{
    /**
     * Return calendar feed.
     */
    public function __invoke()
    {
        $calendar = Calendar::create()
            ->name('Upcoming Events')
            ->description('Description of Calendar');

        $events = \ChrisHardie\CalendarCrawler\Models\Event::all();
        foreach ($events as $event) {
            $ical_event = \Spatie\IcalendarGenerator\Components\Event::create()
                ->name($event->title)
                ->createdAt($event->created_at)
                ->startsAt($event->start_timestamp);

            if (! empty($event->description)) {
                $ical_event->appendProperty(
                    TextProperty::create('X-ALT-DESC;FMTTYPE=text/html', $event->description)
                );

                if (! empty($event->description_textonly)) {
                    $ical_event->description($event->description_textonly);
                }
            }

            $calendar->event($ical_event);
        }

        return response($calendar->get())
            ->header('Content-Type', 'text/calendar')
            ->header('charset', 'utf-8');
    }
}
