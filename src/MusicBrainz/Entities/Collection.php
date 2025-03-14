<?php

declare(strict_types=1);

namespace MusicBrainz\Entities;

use MusicBrainz\MusicBrainz;

/**
 * Represents a MusicBrainz collection object
 * @package MusicBrainz
 */
class Collection implements EntityInterface
{
    public string $id;

    private array $data;

    private MusicBrainz $brainz;

    public function __construct(
        array $collection,
        MusicBrainz $brainz
    ) {
        $this->data   = $collection;
        $this->brainz = $brainz;

        $this->id = isset($collection['id']) ? (string)$collection['id'] : '';
    }

    public function getId(): string
    {
        return $this->id;
    }
}
