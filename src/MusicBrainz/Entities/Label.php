<?php

declare(strict_types=1);

namespace MusicBrainz\Entities;

use MusicBrainz\MusicBrainz;

/**
 * Represents a MusicBrainz label object
 */
class Label implements EntityInterface
{
    public string $id;

    public string $name;

    public string $type;

    public array $aliases;

    public int $score;

    public string $sortName;

    public string $country;

    private array $data;

    private MusicBrainz $brainz;

    public function __construct(
        array $label,
        MusicBrainz $brainz
    ) {
        $this->data   = $label;
        $this->brainz = $brainz;

        $this->id       = (string)($label['id'] ?? '');
        $this->type     = (string)($label['type'] ?? '');
        $this->score    = (int)($label['score'] ?? 0);
        $this->sortName = (string)($label['sort-name'] ?? '');
        $this->name     = (string)($label['name'] ?? '');
        $this->country  = (string)($label['country'] ?? '');
        $this->aliases  = $label['aliases'] ?? [];
    }

    public function getId(): string
    {
        return $this->id;
    }
}
