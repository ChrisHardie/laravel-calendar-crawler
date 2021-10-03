<?php

namespace ChrisHardie\CalendarCrawler;

use Illuminate\Support\Facades\Facade;

/**
 * @see \ChrisHardie\CalendarCrawler\CalendarCrawler
 */
class CalendarCrawlerFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel-calendar-crawler';
    }
}
