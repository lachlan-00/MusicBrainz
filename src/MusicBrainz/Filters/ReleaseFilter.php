<?php

declare(strict_types=1);

namespace MusicBrainz\Filters;

use MusicBrainz\Entities\Release;
use MusicBrainz\MusicBrainz;

/**
 * This is the release filter and it contains
 * an array of valid argument types to be used
 * when querying the MusicBrainz web service.
 */
class ReleaseFilter extends AbstractFilter implements FilterInterface
{
    private const ENTITY = 'release';

    private const LINKS = [
        'area',
        'artist',
        'collection',
        'label',
        'track',
        'track_artist',
        'recording',
        'release-group'
    ];

    /** @var string[] $INCLUDES */
    public const INCLUDES = [
        "artist-credits",
        "labels",
        "recordings",
        "release-groups",
        "media",
        "discids",
        "artist-rels",
        "label-rels",
        "recording-rels",
        "release-rels",
        "release-group-rels",
        "url-rels",
        "work-rels",
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
        'laid',
        'label',
        'lang',
        'mediums',
        'primarytype',
        'puid',
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

    /** @return string[] */
    public function getIncludes(): array
    {
        return self::INCLUDES;
    }

    public function parseResponse(
        array $response,
        MusicBrainz $brainz
    ): array {
        $releases = [];
        if (isset($response['release'])) {
            echo "isset release";
            foreach ($response['release'] as $release) {
                $releases[] = new Release($release, $brainz);
            }
        } elseif (isset($response['releases'])) {
            echo "isset release";
            foreach ($response['releases'] as $release) {
                $releases[] = new Release($release, $brainz);
            }
        } else {
            echo 'else';
            print_r($response);
        }

        return $releases;
    }
}
