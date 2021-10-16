<?php
// config for ChrisHardie/CalendarCrawler
return [
    'default_update_frequency' => 720, // Refresh every 12 hours

    'calendar_name' => 'Calendar of Events',

    'calendar_description' => 'A calendar of events from various sources.',

    // Default URL of calendar ICS feed
    'stream_url' => '/calendar/calendar.ics',

    // Source-specific authentication information
    'auth' => [
        'google' => [
            'api_key' => env('GOOGLE_CAL_API_KEY'),
        ]
    ],
];
