<?php

declare(strict_types=1);

namespace MusicBrainz\Filters;

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
    public const INCLUDES = [];

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
     */
    public function parseResponse(
        array $response,
        MusicBrainz $brainz
    ): array {
        $tags = [];
        foreach ($response['tags'] as $tag) {
            $tags[] = new Tag($tag);
        }

        return $tags;
    }
}
