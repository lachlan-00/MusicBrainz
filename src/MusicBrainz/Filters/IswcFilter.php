<?php

declare(strict_types=1);

namespace MusicBrainz\Filters;

use MusicBrainz\MusicBrainz;
use MusicBrainz\Objects\Iswc;

/**
 * This is the iswc filter and it contains
 * an array of valid argument types to be used
 * when querying the MusicBrainz web service.
 */
class IswcFilter extends AbstractFilter implements FilterInterface
{
    private const ENTITY = 'iswc';

    private const CAN_SEARCH = false;

    /** @var string[] $LINKS */
    private const LINKS = [];

    /** @var string[] $INCLUDES */
    public const INCLUDES = [
        'artists',
        'collection',
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
     */
    public function parseResponse(
        array $response,
        MusicBrainz $brainz
    ): array {
        $iswcs = [];

        foreach ($response['iswcs'] as $iswc) {
            $iswcs[] = $iswc;
        }

        return $iswcs;
    }
}
