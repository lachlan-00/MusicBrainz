<?php

declare(strict_types=1);

namespace MusicBrainz\Objects;

/**
 * Represents a MusicBrainz coordinates object
 * @package MusicBrainz
 */
class Coordinate implements ObjectInterface
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

    public function getData(): Coordinate
    {
        return $this;
    }

    /**
     * Get the object properties as an array
     *
     * @return array{
     *     longitude: ?float,
     *     latitude: ?float
     * }
     */
    public function getProps(): array
    {
        return [
            'longitude' => $this->longitude,
            'latitude' => $this->latitude
        ];
    }
}
