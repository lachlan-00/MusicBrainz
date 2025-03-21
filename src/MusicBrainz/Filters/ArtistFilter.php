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

    private const CAN_SEARCH = true;

    /** @var string[] $LINKS */
    private const LINKS = [
        'area',
        'collection',
        'recording',
        'release-group',
        'release',
        'work',
    ];

    /** @var string[] $INCLUDES */
    public const INCLUDES = [
        'aliases',
        'annotation',
        'area-rels',
        'artist-rels',
        'discids',
        'event-rels',
        'genre-rels',
        'genres',
        'instrument-rels',
        'label-rels',
        'media',
        'place-rels',
        'ratings',
        'recording-rels',
        'recordings',
        'release-group-rels',
        'release-groups',
        'release-rels',
        'releases',
        'series-rels',
        'tags',
        'url-rels',
        'user-ratings',
        'user-tags',
        'various-artists',
        'work-rels',
        'works',
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
        'name',
        'sortname',
        'tag',
        'title',
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

    public function canSearch(): bool
    {
        return self::CAN_SEARCH;
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
        MusicBrainz $brainz,
    ): array {
        $results = [];
        if (isset($response['artist'])) {
            foreach ($response['artist'] as $artist) {
                $results[] = new Artist((array)$artist, $brainz);
            }
        } elseif (isset($response['artists'])) {
            foreach ($response['artists'] as $artist) {
                $results[] = new Artist((array)$artist, $brainz);
            }
        } else {
            throw new Exception(sprintf('No %s found', self::ENTITY));
        }

        return $results;
    }
}
