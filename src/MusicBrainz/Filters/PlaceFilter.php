<?php

declare(strict_types=1);

namespace MusicBrainz\Filters;

use MusicBrainz\Entities\Place;
use MusicBrainz\Exception;
use MusicBrainz\MusicBrainz;

/**
 * This is the place filter and it contains
 * an array of valid argument types to be used
 * when querying the MusicBrainz web service.
 */
class PlaceFilter extends AbstractFilter implements FilterInterface
{
    private const ENTITY = 'place';

    private const CAN_SEARCH = true;

    /** @var string[] $LINKS */
    private const LINKS = [
        'area',
        'collection',
        'release',
    ];

    /** @var string[] $INCLUDES */
    public const INCLUDES = [
        'aliases',
        'area-rels',
        'artist-rels',
        'event-rels',
        'genre-rels',
        'genres',
        'instrument-rels',
        'label-rels',
        'place-rels',
        'ratings',
        'recording-rels',
        'release-group-rels',
        'release-rels',
        'series-rels',
        'tags',
        'url-rels',
        'user-ratings',
        'user-tags',
        'work-rels',
    ];

    /** @var string[] $validArgTypes */
    protected array $validArgTypes = [
        'alias',
        'begin',
        'code',
        'comment',
        'country',
        'end',
        'ended',
        'ipi',
        'label',
        'labelaccent',
        'laid',
        'name',
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
     * @return Place[]
     * @throws Exception
     */
    public function parseResponse(
        array $response,
        MusicBrainz $brainz
    ): array {
        $places = [];

        foreach ($response['places'] as $place) {
            $places[] = new Place((array)$place, $brainz);
        }

        return $places;
    }
}
