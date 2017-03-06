<?php

declare(strict_types=1);

namespace JourneyMonitor\Monitor\JobRunner;

class HarTransformer
{
    /**
     * @param string $originalHar The original HAR JSON structure
     * @param string[] $urls Array of URLs that defines where to split the HAR into multiple pages
     * @return string The transformed HAR JSON structure
     */
    public function splitIntoMultiplePages(string $originalHar, $urls)
    {
        $harObject = json_decode($originalHar);
        $urls = $this->normalizeUrls($urls);

        $pageStartedDateTimes = [];
        $pageStartedDateTimes[] = $harObject->log->entries[0]->startedDateTime;
        $currentIndex = 0;

        foreach ($harObject->log->entries as $entry) {
            foreach ($urls as $index => $url) {
                if ($index > $currentIndex && $url !== $urls[$currentIndex] && $entry->request->url === $url) {
                    $pageStartedDateTimes[] = $entry->startedDateTime;
                    $currentIndex = $index;
                }
            }
            $entry->pageref = 'Request '. ($currentIndex + 1) . ': ' . $urls[$currentIndex];
        }

        $pages = [];
        foreach ($pageStartedDateTimes as $index => $pageStartedDateTime) {
            $page = new \stdClass();
            $page->id = 'Request '. ($index + 1) . ': ' . $urls[$index];
            $page->startedDateTime = $pageStartedDateTime;
            $page->title = 'Request '. ($index + 1) . ': ' . $urls[$index];
            $pageTimings = new \stdClass();
            $pageTimings->comment = '';
            $page->pageTimings = $pageTimings;
            $page->comment = '';
            $pages[] = $page;
        }

        $harObject->log->pages = $pages;
        return json_encode($harObject);
    }

    private function normalizeUrls($urls)
    {
        $normalizedUrls = [];
        foreach ($urls as $url) {
            if (strstr($url, '#')) {
                $url = substr($url, 0, strpos($url, '#'));
            }
            $normalizedUrls[] = $url;
        }
        return $normalizedUrls;
    }
}
