<?php

declare(strict_types=1);

namespace MusicBrainz;

/**
 * Represents a MusicBrainz collection object
 * @package MusicBrainz
 */
class Collection
{
    public string $id;

    private array $data;

    private MusicBrainz $brainz;

    /**
     * @param array $collection
     * @param MusicBrainz $brainz
     */
    public function __construct(array $collection, MusicBrainz $brainz)
    {
        $this->data   = $collection;
        $this->brainz = $brainz;

        $this->id = isset($collection['id']) ? (string)$collection['id'] : '';
    }
}
