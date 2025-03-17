<?php

declare(strict_types=1);

namespace MusicBrainz\Filters;

use MusicBrainz\Entities\ReleaseGroup;
use MusicBrainz\Exception;
use MusicBrainz\MusicBrainz;

/**
 * This is the release group filter and it contains
 * an array of valid argument types to be used
 * when querying the MusicBrainz web service.
 */
class ReleaseGroupFilter extends AbstractFilter implements FilterInterface
{
    private const ENTITY = 'release-group';

    private const LINKS = [
        'artist',
        'collection',
        'release'
    ];

    /** @var string[] $validArgTypes */
    protected array $validArgTypes = [
        'arid',
        'artist',
        'artistname',
        'comment',
        'creditname',
        'primarytype',
        'rgid',
        'releasegroup',
        'releasegroupaccent',
        'releases',
        'release',
        'reid',
        'secondarytype',
        'status',
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

    /**
     * @return ReleaseGroup[]
     * @throws Exception
     */
    public function parseResponse(
        array $response,
        MusicBrainz $brainz
    ): array {

        if (!isset($response['release-groups'])) {
            throw new Exception('No release groups found');
        }

        $releaseGroups = [];
        foreach ($response['release-groups'] as $releaseGroup) {
            $releaseGroups[] = new ReleaseGroup($releaseGroup, $brainz);
        }

        return $releaseGroups;
    }
}
