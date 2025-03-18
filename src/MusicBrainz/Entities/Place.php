<?php

declare(strict_types=1);

namespace MusicBrainz\Entities;

use MusicBrainz\Exception;
use MusicBrainz\MusicBrainz;
use MusicBrainz\Objects\Coordinate;
use MusicBrainz\Objects\LifeSpan;

/**
 * Represents a MusicBrainz place object
 * @package MusicBrainz
 */
class Place extends AbstractEntity implements EntityInterface
{
    public string $id;

    public string $name;

    public string $address;

    public string $type_id;

    public string $type;

    public LifeSpan $life_span;

    public Area $area;

    public Coordinate $coordinates;

    public string $disambiguation;

    private array $data;

    private MusicBrainz $brainz;

    /**
     * @param array $place
     * @param MusicBrainz $brainz
     * @throws Exception
     */
    public function __construct(
        array $place,
        MusicBrainz $brainz
    ) {
        if (
            !isset($place['id']) ||
            !$this->hasValidId($place['id'])
        ) {
            throw new Exception('Can not create place object. Missing valid MBID');
        }

        $this->brainz         = $brainz;
        $this->data           = $place;
        $this->id             = $place['id'];
        $this->name           = (string)($place['name'] ?? '');
        $this->address        = (string)($place['address'] ?? '');
        $this->type_id        = (string)($place['type-id'] ?? '');
        $this->type           = (string)($place['type'] ?? '');
        $this->area           = $place['area'];
        $this->life_span      = $place['life-span'];
        $this->coordinates    = $place['coordinates'];
        $this->disambiguation = (string)($place['disambiguation'] ?? '');
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getData(): array
    {
        return $this->data;
    }
}
