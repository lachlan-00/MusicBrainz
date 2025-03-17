<?php

declare(strict_types=1);

namespace MusicBrainz\Filters;

use MusicBrainz\Entities\Genre;
use MusicBrainz\MusicBrainz;

/**
 * This is the tag filter and it contains
 * an array of valid argument types to be used
 * when querying the MusicBrainz web service.
 */
class GenreFilter extends AbstractFilter implements FilterInterface
{
    private const ENTITY = 'genre';

    private const LINKS = [];

    /** @var string[] $INCLUDES */
    public const INCLUDES = [];

    /** @var string[] $validArgTypes */
    protected array $validArgTypes = [
        'genre',
    ];

    public function getEntity(): string
    {
        return self::ENTITY;
    }

    public function hasLink(string $entity): bool
    {
        return in_array($entity, self::LINKS);
    }

    /** @return string[] */
    public function getIncludes(): array
    {
        return self::INCLUDES;
    }

    /**
     * @return Genre[]
     */
    public function parseResponse(
        array $response,
        MusicBrainz $brainz
    ): array {
        $genres = [];
        foreach ($response['genres'] as $genre) {
            $genres[] = new Genre($genre, $brainz);
        }

        return $genres;
    }
}
