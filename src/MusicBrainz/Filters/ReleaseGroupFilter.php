<?php

declare(strict_types=1);

namespace MusicBrainz\Filters;

use MusicBrainz\Exception;
use MusicBrainz\MusicBrainz;
use MusicBrainz\ReleaseGroup;

/**
 * This is the release group filter and it contains
 * an array of valid argument types to be used
 * when querying the MusicBrainz web service.
 */
class ReleaseGroupFilter extends AbstractFilter implements FilterInterface
{
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
        'type'
    ];

    public function getEntity(): string
    {
        return 'release-group';
    }

    /**
     * @return ReleaseGroup[]
     * @throws Exception
     */
    public function parseResponse(array $response, MusicBrainz $brainz): array
    {

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
