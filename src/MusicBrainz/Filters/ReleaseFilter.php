<?php

declare(strict_types=1);

namespace MusicBrainz\Filters;

use MusicBrainz\Entities\Release;
use MusicBrainz\MusicBrainz;

/**
 * This is the release filter and it contains
 * an array of valid argument types to be used
 * when querying the MusicBrainz web service.
 */
class ReleaseFilter extends AbstractFilter implements FilterInterface
{
    /** @var string[] $validArgTypes */
    protected array $validArgTypes = [
        'arid',
        'artist',
        'artistname',
        'asin',
        'barcode',
        'catno',
        'comment',
        'country',
        'creditname',
        'date',
        'discids',
        'discidsmedium',
        'format',
        'laid',
        'label',
        'lang',
        'mediums',
        'primarytype',
        'puid',
        'reid',
        'release',
        'releaseaccent',
        'rgid',
        'script',
        'secondarytype',
        'status',
        'tag',
        'tracks',
        'tracksmedium',
        'type',
    ];

    public function getEntity(): string
    {
        return 'release';
    }

    public function parseResponse(
        array $response,
        MusicBrainz $brainz
    ): array {
        $releases = [];
        if (isset($response['release'])) {
            foreach ($response['release'] as $release) {
                $releases[] = new Release($release, $brainz);
            }
        } elseif (isset($response['releases'])) {
            foreach ($response['releases'] as $release) {
                $releases[] = new Release($release, $brainz);
            }
        }

        return $releases;
    }
}
