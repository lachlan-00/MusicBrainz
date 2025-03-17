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
    private const ENTITY = 'artist';

    private const LINKS = [
        'area',
        'collection',
        'recording',
        'release-group',
        'release',
        'work'
    ];

    /** @var string[] $INCLUDES */
    public const INCLUDES = [
        'aliases',
        'genres',
        'ratings',
        'tags',
        'user-ratings',
        'user-tags',
    ];

    /** @var string[] $validArgTypes */
    protected array $validArgTypes = [
        'alias',
        'arid',
        'artist',
        'artistaccent',
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
        return self::ENTITY;
    }

    public function hasLink(string $entity): bool
    {
        return in_array($entity, self::LINKS);
    }

    /** @return string[] */
    public function getIncludes(): array
    {
        return self::INCLUDES;
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
