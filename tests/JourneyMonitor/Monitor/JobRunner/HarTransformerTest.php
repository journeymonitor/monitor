<?php

namespace JourneyMonitor\Monitor\JobRunner;

use PHPUnit_Framework_TestCase;

class HarTransformerTest extends PHPUnit_Framework_TestCase
{
    public function testSplitIntoMultiplePages()
    {
        $originalHar = file_get_contents(__DIR__ . '/../../../fixtures/testrun.original.har.json');
        $transformedHar = file_get_contents(__DIR__ . '/../../../fixtures/testrun.transformed.har.json');

        // Working around the fact that the JSON in the file is "beautified".
        $transformedHar = json_encode(json_decode($transformedHar));

        $urls = [
            0 => 'https://www.galeria-kaufhof.de/',
            1 => 'https://www.galeria-kaufhof.de/search?q=hose',
            2 => 'https://www.galeria-kaufhof.de/',
        ];

        $ht = new HarTransformer();

        $this->assertEquals($transformedHar, $ht->splitIntoMultiplePages($originalHar, $urls));

        // Verifies that the operation is idempotent
        $this->assertEquals($transformedHar, $ht->splitIntoMultiplePages($transformedHar, $urls));
    }
}
