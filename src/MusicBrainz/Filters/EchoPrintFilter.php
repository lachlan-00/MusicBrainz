<?php

declare(strict_types=1);

namespace MusicBrainz\Filters;

use MusicBrainz\Exception;
use MusicBrainz\MusicBrainz;

/**
 * This is the echoprint filter and it contains
 * an array of valid argument types to be used
 * when querying the MusicBrainz web service.
 */
class EchoPrintFilter extends AbstractFilter implements FilterInterface
{
    private const ENTITY = 'echoprint';

    private const CAN_SEARCH = false;

    /** @var string[] $LINKS */
    private const LINKS = [];

    /** @var string[] $INCLUDES */
    public const INCLUDES = [
        'area-rels',
        'artist-rels',
        'artists',
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
     * @return string[]
     * @throws Exception
     */
    public function parseResponse(
        array $response,
        MusicBrainz $brainz,
    ): array {
        if (!isset($response['echoprints'])) {
            throw new Exception(sprintf('No %s found', self::ENTITY));
        }

        $results = [];
        foreach ($response['echoprints'] as $echoprint) {
            $results[] = $echoprint;
        }

        return $results;
    }
}
