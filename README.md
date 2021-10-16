# Laravel package to enable crawling web pages for event data, generating corresponding ICS feeds

[![Latest Version on Packagist](https://img.shields.io/packagist/v/chrishardie/laravel-calendar-crawler.svg?style=flat-square)](https://packagist.org/packages/chrishardie/laravel-calendar-crawler)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/chrishardie/laravel-calendar-crawler/run-tests?label=tests)](https://github.com/chrishardie/laravel-calendar-crawler/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/chrishardie/laravel-calendar-crawler/Check%20&%20fix%20styling?label=code%20style)](https://github.com/chrishardie/laravel-calendar-crawler/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/chrishardie/laravel-calendar-crawler.svg?style=flat-square)](https://packagist.org/packages/chrishardie/laravel-calendar-crawler)

## Installation

You can install the package via composer:

```bash
composer require chrishardie/laravel-calendar-crawler
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --provider="ChrisHardie\CalendarCrawler\CalendarCrawlerServiceProvider" --tag="calendar-crawler-migrations"
php artisan migrate
```

You can publish the config file with:
```bash
php artisan vendor:publish --provider="ChrisHardie\CalendarCrawler\CalendarCrawlerServiceProvider" --tag="calendar-crawler-config"
```

This is the contents of the published config file:

```php
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
```

## Usage

1) Use an admin interface, artisan tinker session, DB seeder file or direct database call to add calendar sources. The main fields needed are:
* Name
* Type (currently, `GoogleCalendar` or `FacebookPage`)
* Home URL
* Location (either the Google Calendar calendar ID or the Facebook Page's numeric page ID)

```php
        DB::table('calendar_sources')->insert([
            'name' => 'Your Local Government',
            'type' => 'GoogleCalendar',
            'home_url' => 'https://www.government.gov/',
            'location' => 'googlecalendarid@gmail.com',
        ]);

        DB::table('calendar_sources')->insert([
            'name' => 'Cool Nonprofit Organization',
            'type' => 'FacebookPage',
            'home_url' => 'https://www.facebook.com/orgname/',
            'location' => '12345678',
        ]);

```

2) If you specify any GoogleCalendar sources, you will need to [create an API key](https://console.cloud.google.com/apis/credentials) and then define a `GOOGLE_CAL_API_KEY` with that key as the value in your `.env` file.
3) The sources provided will be crawled according to the update frequency specified.
4) Use the event data elsewhere within your Laravel application directly, or retrieve an ICS calendar feed of events at the `stream_url` location specified.

Crawling issues, errors and notices will be written to the log stack configured. Consider using a Slack channel for convenience.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Credits

- [Chris Hardie](https://github.com/ChrisHardie)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
