<?php

declare(strict_types=1);

namespace MusicBrainz\Filters;

use MusicBrainz\Entities\Release;
use MusicBrainz\Exception;
use MusicBrainz\MusicBrainz;

/**
 * This is the release filter and it contains
 * an array of valid argument types to be used
 * when querying the MusicBrainz web service.
 */
class ReleaseFilter extends AbstractFilter implements FilterInterface
{
    private const ENTITY = 'release';

    private const CAN_SEARCH = true;

    /** @var string[] $LINKS */
    private const LINKS = [
        'area',
        'artist',
        'collection',
        'label',
        'recording',
        'release-group',
        'track_artist',
        'track',
    ];

    /** @var string[] $INCLUDES */
    public const INCLUDES = [
        'aliases',
        'annotation',
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
        'arid',
        'artist',
        'artistname',
        'asin',
        'barcode',
        'catno',
        'comment',
        'country',
        'creditname',
        'date',
        'discids',
        'discidsmedium',
        'format',
        'label',
        'laid',
        'lang',
        'mediums',
        'primarytype',
        'reid',
        'release',
        'releaseaccent',
        'rgid',
        'script',
        'secondarytype',
        'status',
        'tag',
        'tracks',
        'tracksmedium',
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
     * @return Release[]
     * @throws Exception
     */
    public function parseResponse(
        array $response,
        MusicBrainz $brainz,
    ): array {
        $results = [];
        if (isset($response['release'])) {
            foreach ($response['release'] as $release) {
                $results[] = new Release((array)$release, $brainz);
            }
        } elseif (isset($response['releases'])) {
            foreach ($response['releases'] as $release) {
                $results[] = new Release((array)$release, $brainz);
            }
        } else {
            throw new Exception(sprintf('No %s found', self::ENTITY));
        }

        return $results;
    }
}
