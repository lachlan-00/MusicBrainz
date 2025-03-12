<?php

namespace MusicBrainz\Filters;

use MusicBrainz\MusicBrainz;

/**
 * Class FilterInterface
 * @package MusicBrainz\Filters
 */
interface FilterInterface
{
    /**
     * @return string
     */
    public function getEntity();

    /**
     * @return array
     */
    public function createParameters(array $params = []);

    /**
     * @return mixed An array of the Filter's Music Brainz entity objects
     */
    public function parseResponse(array $response, MusicBrainz $brainz);
}
