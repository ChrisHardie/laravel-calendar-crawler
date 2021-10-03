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
];
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Chris Hardie](https://github.com/ChrisHardie)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
