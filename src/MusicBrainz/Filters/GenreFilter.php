<?php

declare(strict_types=1);

namespace MusicBrainz\Filters;

use MusicBrainz\Entities\Genre;
use MusicBrainz\MusicBrainz;
use MusicBrainz\Tag;

/**
 * This is the tag filter and it contains
 * an array of valid argument types to be used
 * when querying the MusicBrainz web service.
 */
class GenreFilter extends AbstractFilter implements FilterInterface
{
    /** @var string[] $validArgTypes */
    protected array $validArgTypes = [
        'genre',
    ];

    public function getEntity(): string
    {
        return 'genre';
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
