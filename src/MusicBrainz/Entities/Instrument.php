<?php

declare(strict_types=1);

namespace MusicBrainz\Entities;

use MusicBrainz\Exception;
use MusicBrainz\MusicBrainz;

/**
 * Represents a MusicBrainz event object
 * @package MusicBrainz
 */
class Instrument extends AbstractEntity implements EntityInterface
{
    public string $id;

    public string $name;

    public ?string $type = null;

    public string $type_id;

    public string $disambiguation;

    public string $description;

    private array $data;

    private MusicBrainz $brainz;

    /**
     * @param array $instrument
     * @param MusicBrainz $brainz
     * @throws Exception
     */
    public function __construct(
        array $instrument,
        MusicBrainz $brainz
    ) {
        if (
            !isset($instrument['id']) ||
            !$this->hasValidId($instrument['id'])
        ) {
            throw new Exception('Can not create instrument object. Missing valid MBID');
        }

        $this->brainz         = $brainz;
        $this->data           = $instrument;
        $this->id             = $instrument['id'];
        $this->name           = (string)($instrument['name'] ?? '');
        $this->type_id        = (string)($instrument['type-id'] ?? '');
        $this->type           = (string)($instrument['type'] ?? '');
        $this->description    = (string)($instrument['description'] ?? '');
        $this->disambiguation = (string)($instrument['disambiguation'] ?? '');
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
