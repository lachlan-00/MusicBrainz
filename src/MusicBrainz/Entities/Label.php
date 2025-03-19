<?php

declare(strict_types=1);

namespace MusicBrainz\Entities;

use MusicBrainz\Exception;
use MusicBrainz\MusicBrainz;

/**
 * Represents a MusicBrainz label object
 */
class Label extends AbstractEntity implements EntityInterface
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

    /**
     * @param array $label
     * @param MusicBrainz $brainz
     * @throws Exception
     */
    public function __construct(
        array $label,
        MusicBrainz $brainz
    ) {
        if (
            !isset($label['id']) ||
            !$this->hasValidId($label['id'])
        ) {
            throw new Exception('Can not create label object. Missing valid MBID');
        }

        $this->brainz   = $brainz;
        $this->data     = $label;
        $this->id       = $label['id'];
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

    public function getName(): string
    {
        return $this->name;
    }

    public function getData(): array
    {
        return $this->data;
    }
}
