<?php
// config for ChrisHardie/CalendarCrawler
return [
    'auth' => [
        'google' => [
            'api_key' => env('GOOGLE_CAL_API_KEY'),
        ]
    ],
    'default_update_frequency' => 720, // Refresh every 12 hours

];
