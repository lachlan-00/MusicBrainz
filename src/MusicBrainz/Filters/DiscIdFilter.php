<?php

declare(strict_types=1);

namespace MusicBrainz\Filters;

use MusicBrainz\Entities\DiscId;
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
        'artist-credits',
        'artist-rels',
        'artists',
        'discids',
        'echoprints',
        'isrcs',
        'label-rels',
        'labels',
        'media',
        'recording-level-rels',
        'recording-rels',
        'recordings',
        'release-group-rels',
        'release-groups',
        'release-rels',
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
     */
    public function parseResponse(
        array $response,
        MusicBrainz $brainz
    ): array {
        $discids = [];

        foreach ($response['discids'] as $discid) {
            $discids[] = new DiscId($discid, $brainz);
        }

        return $discids;
    }
}
