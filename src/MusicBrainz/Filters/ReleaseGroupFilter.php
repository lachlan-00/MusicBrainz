<?php

declare(strict_types=1);

namespace MusicBrainz\Filters;

use MusicBrainz\Entities\ReleaseGroup;
use MusicBrainz\Exception;
use MusicBrainz\MusicBrainz;

/**
 * This is the release group filter and it contains
 * an array of valid argument types to be used
 * when querying the MusicBrainz web service.
 */
class ReleaseGroupFilter extends AbstractFilter implements FilterInterface
{
    private const ENTITY = 'release-group';

    private const CAN_SEARCH = true;

    /** @var string[] $LINKS */
    private const LINKS = [
        'artist',
        'collection',
        'release',
    ];

    /** @var string[] $INCLUDES */
    public const INCLUDES = [
        'aliases',
        'annotation',
        'area-rels',
        'artist-credits',
        'artist-rels',
        'artists',
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
        'release-group-rels',
        'release-rels',
        'releases',
        'series-rels',
        'tags',
        'url-rels',
        'user-ratings',
        'user-tags',
        'work-rels',
    ];

    /** @var string[] $validArgTypes */
    protected array $validArgTypes = [
        'arid',
        'artist',
        'artistname',
        'comment',
        'creditname',
        'name',
        'primarytype',
        'reid',
        'release',
        'releasegroup',
        'releasegroupaccent',
        'releases',
        'rgid',
        'secondarytype',
        'status',
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
     * @return ReleaseGroup[]
     * @throws Exception
     */
    public function parseResponse(
        array $response,
        MusicBrainz $brainz,
    ): array {
        if (!isset($response['release-groups'])) {
            throw new Exception(sprintf('No %s found', self::ENTITY));
        }

        $results = [];
        foreach ($response['release-groups'] as $releaseGroup) {
            $results[] = new ReleaseGroup((array)$releaseGroup, $brainz);
        }

        return $results;
    }
}
