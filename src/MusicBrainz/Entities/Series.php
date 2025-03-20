<?php

declare(strict_types=1);

namespace MusicBrainz\Entities;

use MusicBrainz\Exception;
use MusicBrainz\MusicBrainz;

/**
 * Represents a MusicBrainz series object
 * @package MusicBrainz
 */
class Series extends AbstractEntity implements EntityInterface
{
    public string $id;

    public string $name;

    public string $type_id;

    public string $type;

    public string $disambiguation;

    private array $data;

    private MusicBrainz $brainz;

    /**
     * @param array $series
     * @param MusicBrainz $brainz
     * @throws Exception
     */
    public function __construct(
        array $series,
        MusicBrainz $brainz,
    ) {
        if (
            !isset($series['id']) ||
            !$this->hasValidId($series['id'])
        ) {
            throw new Exception('Can not create series object. Missing valid MBID');
        }

        $this->brainz         = $brainz;
        $this->data           = $series;
        $this->id             = $series['id'];
        $this->name           = (string)($series['name'] ?? '');
        $this->type_id        = (string)($series['type-id'] ?? '');
        $this->type           = (string)($series['type'] ?? '');
        $this->disambiguation = (string)($series['disambiguation'] ?? '');
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
