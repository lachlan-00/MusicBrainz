<?php

declare(strict_types=1);

namespace MusicBrainz\Filters;

use MusicBrainz\Entities\DiscId;
use MusicBrainz\Exception;
use MusicBrainz\MusicBrainz;

/**
 * This is the discid filter and it contains
 * an array of valid argument types to be used
 * when querying the MusicBrainz web service.
 */
class DiscIdFilter extends AbstractFilter implements FilterInterface
{
    private const ENTITY = 'discid';

    private const CAN_SEARCH = false;

    /** @var string[] $LINKS */
    private const LINKS = [];

    /** @var string[] $INCLUDES */
    public const INCLUDES = [
        'area-rels',
        'artist-credits',
        'artist-rels',
        'artists',
        'discids',
        'echoprints',
        'event-rels',
        'genre-rels',
        'instrument-rels',
        'isrcs',
        'label-rels',
        'labels',
        'media',
        'place-rels',
        'recording-level-rels',
        'recording-rels',
        'recordings',
        'release-group-rels',
        'release-groups',
        'release-rels',
        'series-rels',
        'url-rels',
        'work-level-rels',
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
     * @return DiscId[]
     * @throws Exception
     */
    public function parseResponse(
        array $response,
        MusicBrainz $brainz,
    ): array {
        if (!isset($response['discids'])) {
            throw new Exception(sprintf('No %s found', self::ENTITY));
        }

        $results = [];
        foreach ($response['discids'] as $discid) {
            $results[] = new DiscId((array)$discid, $brainz);
        }

        return $results;
    }
}
