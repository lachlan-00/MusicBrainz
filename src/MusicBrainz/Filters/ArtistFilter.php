<?php

declare(strict_types=1);

namespace MusicBrainz\Filters;

use MusicBrainz\Entities\Artist;
use MusicBrainz\Exception;
use MusicBrainz\MusicBrainz;

/**
 * This is the artist filter and it contains
 * an array of valid argument types to be used
 * when querying the MusicBrainz web service.
 */
class ArtistFilter extends AbstractFilter implements FilterInterface
{
    /** @var string[] $validArgTypes */
    protected array $validArgTypes = [
        'arid',
        'artist',
        'artistaccent',
        'alias',
        'begin',
        'comment',
        'country',
        'end',
        'ended',
        'gender',
        'ipi',
        'sortname',
        'tag',
        'type',
    ];

    public function getEntity(): string
    {
        return 'artist';
    }

    /**
     * @return Artist[]
     * @throws Exception
     */
    public function parseResponse(
        array $response,
        MusicBrainz $brainz
    ): array {
        $artists = [];
        if (isset($response['artist'])) {
            foreach ($response['artist'] as $artist) {
                $artists[] = new Artist($artist, $brainz);
            }
        } elseif (isset($response['artists'])) {
            foreach ($response['artists'] as $artist) {
                $artists[] = new Artist($artist, $brainz);
            }
        }

        return $artists;
    }
}
