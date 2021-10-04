<?php

namespace ChrisHardie\CalendarCrawler\Sources;

use ChrisHardie\CalendarCrawler\Exceptions\SourceNotCrawlable;
use ChrisHardie\CalendarCrawler\Models\CalendarSource as Source;
use Goutte\Client;
use Illuminate\Support\Facades\Http;

abstract class BaseSource
{
//    abstract public function getEvents(Source $source): \Illuminate\Database\Eloquent\Collection;

    /**
     * @param Source $source
     * @param null   $url
     * @return \Illuminate\Http\Client\Response|null
     * @throws SourceNotCrawlable
     */
    public function getUrl(Source $source, $url = null): ?\Illuminate\Http\Client\Response
    {
        if (empty($source->source_url) && empty($url)) {
            return null;
        }

        if (empty($url)) {
            $url = $source->source_url;
        }

        try {
            return HTTP::get($url);
        } catch (\Exception $e) {
            throw new SourceNotCrawlable(
                'Problem running GET request: ' . $e->getMessage(),
                0,
                $e,
                $source
            );
        }
    }

    /**
     * @param Source $source
     * @param null   $url Optional URL to override default source URL
     * @return \Symfony\Component\DomCrawler\Crawler|null
     * @throws SourceNotCrawlable
     */
    public function getCrawler(Source $source, $url = null): ?\Symfony\Component\DomCrawler\Crawler
    {
        if (empty($source->source_url) && empty($url)) {
            return null;
        }

        if (empty($url)) {
            $url = $source->source_url;
        }

        try {
            $client = new Client();

            return $client->request('GET', $url);
        } catch (\Exception $e) {
            throw new SourceNotCrawlable(
                'Problem running GET request: ' . $e->getMessage(),
                0,
                $e,
                $source
            );
        }
    }

}
