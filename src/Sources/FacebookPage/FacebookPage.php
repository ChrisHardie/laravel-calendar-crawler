<?php

namespace ChrisHardie\CalendarCrawler\Sources\FacebookPage;

use ChrisHardie\CalendarCrawler\Exceptions\SourceNotCrawlable;
use ChrisHardie\CalendarCrawler\Models\CalendarSource;
use ChrisHardie\CalendarCrawler\Sources\BaseSource;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FacebookPage extends BaseSource
{
    /**
     * Fetch a Facebook page's upcoming events and create Events from them.
     *
     * @throws \JsonException
     * @throws SourceNotCrawlable
     */
    public function getEvents(CalendarSource $source): void
    {
        $fbApiUrl = 'https://www.facebook.com/api/graphql/';
        $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.77 Safari/537.36'; // @phpcs:ignore

        $fbSourceParams = [
            'id' => $source->location,
            'scale' => 1,
            'count' => 3,
        ];

        $postData = array_merge(
            ['variables' => json_encode($fbSourceParams, JSON_THROW_ON_ERROR)],
            self::getBaseParams()
        );

        try {
            $response = HTTP::asForm()
                ->withUserAgent($userAgent)
                ->withHeaders([
                    'Origin' => 'https://www.facebook.com/',
                    'Accept-Language' => 'en_us',
                ])
                ->post($fbApiUrl, $postData)
                ->throw()
                ->object();
        } catch (\Exception $e) {
            throw new SourceNotCrawlable('Cannot fetch events from Facebook', 0, $e, $source);
        }

        if (empty($response->data)) {
            throw new SourceNotCrawlable('No data object in event result', 0, null, $source);
        }

        if (! empty($response->data->node->upcoming_events->edges)) {
            foreach ($response->data->node->upcoming_events->edges as $event_node) {
                $event = $event_node->node;

                // Check to make sure it was a valid payload
                if (empty($event->id)) {
                    Log::debug('No valid event info found in a result node');

                    continue;
                }

                try {
                    $eventData = $this->parseEventNode($event);
                } catch (\Exception $e) {
                    throw new SourceNotCrawlable('Cannot parse event node into model', 0, $e, $source);
                }

                if (! $eventData) {
                    continue;
                }

                $source->events()->updateOrCreate(
                    [
                        'source_internal_id' => $event->id,
                    ],
                    $eventData
                );
            }
        }
    }

    /**
     * Take a Facebook API result event node and turn it into an array useful for creating Events
     *
     * @param object $event
     * @return array
     */
    private function parseEventNode(object $event): ?array
    {
        // So embarrassing that there's no absolute start_time timestamp in the return data object
        if ('HAPPENING NOW' === $event->day_time_sentence) {
            return null;
        }

        // "day_time_sentence": "FRI, AUG 13 - AUG 22",
        if (false !== strpos($event->day_time_sentence, ' - ')) {
            return null;
        }

        $event_text_patterns = [
            '/^(.*?)( AND \d+ MORE)?$/',
            '/ AT /',
            '/UNK$/',
            '/ (\d+) ([A-Z]{3})/',
        ];
        $event_text_replacements = [
            '\1',
            ' ',
            '-7',
            ' \2 \1',
        ];

        $event_start_text = preg_replace(
            $event_text_patterns,
            $event_text_replacements,
            $event->day_time_sentence
        );
        $event_start_timestamp = strtotime($event_start_text);
        if (false === $event_start_timestamp) {
            Log::debug('Error trying to convert FB start text to timestamp for event ID ' . $event->id);

            return null;
        }

        $event_place = ! empty($event->event_place->name) ? $event->event_place->name : null;

        return [
            'title' => $event->name,
            'start_timestamp' => Carbon::parse($event_start_text)
                ->setTimezone('UTC'),
            'end_timestamp' => null,
            'description' => null,
            'address_name' => $event_place,
            'url' => $event->url,
            'status' => 'confirmed',
            'last_crawled_at' => Carbon::now(),
        ];
    }

    /**
     * Set the base API parameters that Facebook seems to require
     * @return array
     */
    private static function getBaseParams(): array
    {
        return [
            'MIME Type' => 'application/x-www-form-urlencoded',
            '__a' => '1',
            '__req' => 'g',
            '__beoa' => '0',
            '__pc' => 'EXP2:comet_pkg',
            '__bhv' => '2',
            'dpr' => '1',
            '__ccg' => 'EXCELLENT',
            '__comet_req' => '1',
            'fb_api_caller_class' => 'RelayModern',
            'fb_api_req_friendly_name' => 'PagesCometEventsUpcomingSectionPaginationQuery',
            'server_timestamps' => 'true',
            'doc_id' => '3818743474878187',
        ];
    }
}
