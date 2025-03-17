<?php

declare(strict_types=1);

namespace MusicBrainz\Filters;

use MusicBrainz\Entities\Recording;
use MusicBrainz\Exception;
use MusicBrainz\MusicBrainz;

/**
 * This is the recording filter and it contains
 * an array of valid argument types to be used
 * when querying the MusicBrainz web service.
 */
class RecordingFilter extends AbstractFilter implements FilterInterface
{
    private const ENTITY = 'recording';

    private const LINKS = [
        'artist',
        'collection',
        'release',
        'work'
    ];

    /** @var string[] $INCLUDES */
    public const INCLUDES = [
        'artist-credits',
        'artist',
        'collection',
        'genres',
        'ratings',
        'release',
        'tags',
        'user-ratings',
        'user-tags',
        'work',
    ];

    /** @var string[] $validArgTypes */
    protected array $validArgTypes = [
        'arid',
        'artist',
        'artistname',
        'comment',
        'country',
        'creditname',
        'date',
        'dur',
        'format',
        'isrc',
        'number',
        'position',
        'primarytype',
        'puid',
        'qdur',
        'recording',
        'recordingaccent',
        'reid',
        'release',
        'rgid',
        'rid',
        'secondarytype',
        'status',
        'tag',
        'tnum',
        'tracks',
        'tracksrelease',
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

    /** @return string[] */
    public function getIncludes(): array
    {
        return self::INCLUDES;
    }

    /**
     * @throws Exception
     */
    public function parseResponse(
        array $response,
        MusicBrainz $brainz
    ): array {
        $recordings = [];

        if (isset($response['recording'])) {
            foreach ($response['recording'] as $recording) {
                $recordings[] = new Recording($recording, $brainz);
            }
        } elseif (isset($response['recordings'])) {
            foreach ($response['recordings'] as $recording) {
                $recordings[] = new Recording($recording, $brainz);
            }
        }

        if ($recordings === []) {
            throw new Exception('No search results found');
        }

        return $recordings;
    }
}
