<?php

declare(strict_types=1);

namespace MusicBrainz\Filters;

use MusicBrainz\Exception;
use MusicBrainz\MusicBrainz;
use MusicBrainz\Objects\Tag;

/**
 * This is the tag filter and it contains
 * an array of valid argument types to be used
 * when querying the MusicBrainz web service.
 */
class TagFilter extends AbstractFilter implements FilterInterface
{
    private const ENTITY = 'tag';

    private const CAN_SEARCH = true;

    /** @var string[] $LINKS */
    private const LINKS = [];

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
        'series-rels',
        'url-rels',
        'work-rels',
    ];

    /** @var string[] $validArgTypes */
    protected array $validArgTypes = [
        'tag',
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
     * @return Tag[]
     * @throws Exception
     */
    public function parseResponse(
        array $response,
        MusicBrainz $brainz,
    ): array {
        if (!isset($response['tags'])) {
            throw new Exception(sprintf('No %s found', self::ENTITY));
        }

        $results = [];
        foreach ($response['tags'] as $tag) {
            /** @var array{name: string|null, count: string|null} $tag */
            $results[] = new Tag((array)$tag);
        }

        return $results;
    }
}
