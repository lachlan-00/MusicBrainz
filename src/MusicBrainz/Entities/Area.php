<?php

declare(strict_types=1);

namespace MusicBrainz\Entities;

use MusicBrainz\Exception;
use MusicBrainz\MusicBrainz;
use MusicBrainz\Objects\LifeSpan;

/**
 * Represents a MusicBrainz area object
 * @package MusicBrainz
 */
class Area extends AbstractEntity implements EntityInterface
{
    public string $id;

    public string $name;

    public string $sort_name;

    public string $type_id;

    public string $type;

    public LifeSpan $life_span;

    /** @var string[] $iso_3166_1_codes */
    public array $iso_3166_1_codes;

    public string $disambiguation;

    private array $data;

    private MusicBrainz $brainz;

    /**
     * @param array $area
     * @param MusicBrainz $brainz
     * @throws Exception
     */
    public function __construct(
        array $area,
        MusicBrainz $brainz
    ) {
        if (
            !isset($area['id']) ||
            !$this->hasValidId($area['id'])
        ) {
            throw new Exception('Can not create area object. Missing valid MBID');
        }

        $this->brainz           = $brainz;
        $this->data             = $area;
        $this->id               = $area['id'];
        $this->name             = (string)($area['name'] ?? '');
        $this->sort_name        = (string)($area['sort-name'] ?? '');
        $this->type_id          = (string)($area['type-id'] ?? '');
        $this->type             = (string)($area['type'] ?? '');
        $this->iso_3166_1_codes = $area['iso-3166-1-codes'] ?? [];
        $this->life_span        = $area['life-span'];
        $this->disambiguation   = (string)($area['disambiguation'] ?? '');
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
