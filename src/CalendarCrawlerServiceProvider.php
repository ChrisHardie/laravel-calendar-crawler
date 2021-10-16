<?php

namespace ChrisHardie\CalendarCrawler;

use ChrisHardie\CalendarCrawler\Commands\CalendarCrawlerCommand;
use ChrisHardie\CalendarCrawler\Http\CalendarController;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Route;
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
            ->hasMigrations(['create_events_table', 'create_calendar_sources_table'])
            ->hasCommand(CalendarCrawlerCommand::class);
    }

    public function packageRegistered(): void
    {
        $this->registerRouteMacro();
    }

    public function packageBooted(): void
    {
        $this->scheduleEventUpdates();
    }

    protected function scheduleEventUpdates(): void
    {
        $this->app->afterResolving(Schedule::class, function (Schedule $schedule) {
            $schedule->command('calcrawl:update')->everyThirtyMinutes();
        });
    }

    protected function registerRouteMacro(): self
    {
        Route::macro('calendarstream', function () {
            Route::get(config('calendar-crawler.stream_url'), '\\' . CalendarController::class)
                ->name('calendar.stream');
        });

        return $this;
    }
}
