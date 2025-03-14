<?php

declare(strict_types=1);

namespace MusicBrainz\Entities;

use MusicBrainz\MusicBrainz;

/**
 * Represents a MusicBrainz tag object
 * @package MusicBrainz
 */
class Genre implements EntityInterface
{
    public string $id;

    public string $name;

    public string $disambiguation;

    private array $data;

    private MusicBrainz $brainz;

    /**
     * @param array $tag
     * @param MusicBrainz $brainz
     */
    public function __construct(
        array $tag,
        MusicBrainz $brainz
    ) {
        $this->data   = $tag;
        $this->brainz = $brainz;

        $this->id             = (string)($tag['id'] ?? '');
        $this->name           = (string)($tag['name'] ?? '');
        $this->disambiguation = (string)($tag['disambiguation'] ?? '');

    }

    public function getId(): string
    {
        return $this->id;
    }
}
