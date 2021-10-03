<?php

namespace ChrisHardie\CalendarCrawler\Commands;

use Illuminate\Console\Command;

class CalendarCrawlerCommand extends Command
{
    public $signature = 'laravel-calendar-crawler';

    public $description = 'My command';

    public function handle()
    {
        $this->comment('All done');
    }
}
