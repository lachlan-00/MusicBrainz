<?php

declare(strict_types=1);

namespace MusicBrainz\Objects;

use MusicBrainz\MusicBrainz;

/**
 * Represents a MusicBrainz coordinates object
 * @package MusicBrainz
 */
class Coordinates
{
    public ?float $longitude = null;

    public ?float $latitude = null;

    /**
     * @param array{
     *     ended: ?bool,
     *     longitude: ?float,
     *     latitude: ?float
     * } $tag
     */
    public function __construct(
        array $tag
    ) {
        $this->longitude = ($tag['longitude'] ?? null);
        $this->latitude  = ($tag['latitude'] ?? null);
    }
}
