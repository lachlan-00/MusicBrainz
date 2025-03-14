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
    /** @var string[] $validArgTypes */
    protected array $validArgTypes = [
        'tag',
    ];

    public function getEntity(): string
    {
        return 'tag';
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
