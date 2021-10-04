<?php

namespace ChrisHardie\CalendarCrawler\Commands;

use ChrisHardie\CalendarCrawler\Models\CalendarSource;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CalendarCrawlerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calcrawl:update {id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crawl calendar sources for events';

    public function handle()
    {
        if (! empty($this->argument('id'))) {
            $sources = CalendarSource::find($this->argument('id'))->get();
        } else {
            $sources = CalendarSource::checkable()->orderBy('last_check_at', 'asc')->get();
        }

        $source_class_base_paths = [
            "ChrisHardie\CalendarCrawler\Sources\\",
            "App\CalendarSources\\",
        ];

        if (! $sources) {
            Log::debug('No sources found for checking');

            return;
        }

        Log::debug('Checking ' . $sources->count() . ' sources for updates.');

        foreach ($sources as $source) {
            foreach ($source_class_base_paths as $base_path) {
                $source_class_path = $base_path . $source->type . '\\' . $source->type;
                Log::debug('Looking for path: ' . $source_class_path);
                if (class_exists($source_class_path)) {
                    $source_class = new $source_class_path();

                    $source->last_check_at = Carbon::now();
                    $source->save();

                    try {
                        $source_class->getEvents($source);

                        if (0 < $source->fail_count) {
                            Log::info(sprintf(
                                'Updating events for source `%s` was successful after %d %s.',
                                $source->name,
                                $source->fail_count,
                                Str::plural('failure', $source->fail_count)
                            ));
                        }

                        $source->update([
                            'last_success_at' => Carbon::now(),
                            'fail_count' => 0,
                            'next_check_after' => null,
                        ]);
                    } catch (\Exception $e) {
                        report($e);
                    }
                }
            }
        }
    }
}
