<?php

namespace ChrisHardie\CalendarCrawler;

use ChrisHardie\CalendarCrawler\Commands\CalendarCrawlerCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

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
            ->hasMigration('create_laravel-calendar-crawler_table')
            ->hasCommand(CalendarCrawlerCommand::class);
    }
}
