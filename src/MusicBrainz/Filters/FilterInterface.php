<?php

namespace MusicBrainz\Filters;

use MusicBrainz\MusicBrainz;

/**
 * Class FilterInterface
 * @package MusicBrainz\Filters
 */
interface FilterInterface
{
    public function getEntity(): string;

    public function hasLink(string $entity): bool;

    public function canSearch(): bool;

    /**
     * https://musicbrainz.org/doc/MusicBrainz_API#Linked_entities
     * @return string[]
     */
    public function getIncludes(): array;

    public function createParameters(array $params = []): array;

    /**
     * Return an array of the Filter's MusicBrainz entity objects
     */
    public function parseResponse(
        array $response,
        MusicBrainz $brainz
    ): array;
}
