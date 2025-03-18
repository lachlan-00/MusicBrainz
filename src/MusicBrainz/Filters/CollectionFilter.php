<?php

declare(strict_types=1);

namespace MusicBrainz\Filters;

use MusicBrainz\Entities\Collection;
use MusicBrainz\Exception;
use MusicBrainz\MusicBrainz;

/**
 * This is the collection filter and it contains
 * an array of valid argument types to be used
 * when querying the MusicBrainz web service.
 */
class CollectionFilter extends AbstractFilter implements FilterInterface
{
    private const ENTITY = 'collection';

    private const CAN_SEARCH = true;

    /** @var string[] $LINKS */
    private const LINKS = [
        'area',
        'artist',
        'editor',
        'event',
        'label',
        'place',
        'recording',
        'release',
        'release-group',
        'work',
    ];

    /** @var string[] $INCLUDES */
    public const INCLUDES = [
        'area-rels',
        'artist-rels',
        'event-rels',
        'genre-rels',
        'instrument-rels',
        'label-rels',
        'place-rels',
        'recording-rels',
        'release-group-rels',
        'release-rels',
        'releases',
        'series-rels',
        'url-rels',
        'user-collections',
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
     * @return Collection[]
     * @throws Exception
     */
    public function parseResponse(
        array $response,
        MusicBrainz $brainz
    ): array {
        $collections = [];

        foreach ($response['collections'] as $collection) {
            $collections[] = new Collection((array)$collection, $brainz);
        }

        return $collections;
    }
}
