<?php

namespace ChrisHardie\CalendarCrawler;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use ChrisHardie\CalendarCrawler\Commands\CalendarCrawlerCommand;

class CalendarCrawlerServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-calendar-crawler')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigrations(['create_events_table', 'create_calendar_sources_table'])
            ->hasCommand(CalendarCrawlerCommand::class);
    }
}
