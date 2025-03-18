<?php

declare(strict_types=1);

namespace MusicBrainz\Filters;

use MusicBrainz\Entities\Work;
use MusicBrainz\Exception;
use MusicBrainz\MusicBrainz;

/**
 * This is the work filter and it contains
 * an array of valid argument types to be used
 * when querying the MusicBrainz web service.
 */
class WorkFilter extends AbstractFilter implements FilterInterface
{
    private const ENTITY = 'work';

    private const CAN_SEARCH = true;

    /** @var string[] $LINKS */
    private const LINKS = [
        'artist',
        'collection',
    ];

    /** @var string[] $INCLUDES */
    public const INCLUDES = [
        'aliases',
        'annotation',
        'artist-rels',
        'artists', // sub queries
        'label-rels',
        'ratings',
        'recording-rels',
        'release-group-rels',
        'release-rels',
        'tags',
        'url-rels',
        'user-ratings', // misc
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
     * @return Work[]
     * @throws Exception
     */
    public function parseResponse(
        array $response,
        MusicBrainz $brainz
    ): array {
        $works = [];

        foreach ($response['works'] as $work) {
            $works[] = new Work($work, $brainz);
        }

        return $works;
    }
}
