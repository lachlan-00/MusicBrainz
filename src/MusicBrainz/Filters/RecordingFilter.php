<?php

namespace MusicBrainz\Filters;

use MusicBrainz\Exception;
use MusicBrainz\MusicBrainz;
use MusicBrainz\Recording;

/**
 * This is the recording filter and it contains
 * an array of valid argument types to be used
 * when querying the MusicBrainz web service.
 */
class RecordingFilter extends AbstractFilter implements FilterInterface
{
    public $validArgTypes = [
        'arid',
        'artist',
        'artistname',
        'creditname',
        'comment',
        'country',
        'date',
        'dur',
        'format',
        'isrc',
        'number',
        'position',
        'primarytype',
        'puid',
        'qdur',
        'recording',
        'recordingaccent',
        'reid',
        'release',
        'rgid',
        'rid',
        'secondarytype',
        'status',
        'tnum',
        'tracks',
        'tracksrelease',
        'tag',
        'type'
    ];

    /**
     * @return string
     */
    public function getEntity()
    {
        return 'recording';
    }

    /**
     * @throws Exception
     * @return array
     */
    public function parseResponse(array $response, MusicBrainz $brainz)
    {
        $recordings = [];

        if (isset($response['recording'])) {
            foreach ($response['recording'] as $recording) {
                $recordings[] = new Recording($recording, $brainz);
            }
        } elseif (isset($response['recordings'])) {
            foreach ($response['recordings'] as $recording) {
                $recordings[] = new Recording($recording, $brainz);
            }
        }

        if ($recordings === []) {
            throw new Exception('No search results found');
        }

        return $recordings;
    }
}
