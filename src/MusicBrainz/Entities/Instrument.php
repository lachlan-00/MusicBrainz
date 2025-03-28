<?php

declare(strict_types=1);

namespace MusicBrainz\Entities;

use MusicBrainz\Exception;
use MusicBrainz\MusicBrainz;

/**
 * Represents a MusicBrainz instrument object
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

    protected MusicBrainz $brainz;

    /**
     * @param array $instrument
     * @param MusicBrainz $brainz
     * @throws Exception
     */
    public function __construct(
        array $instrument,
        MusicBrainz $brainz,
    ) {
        if (
            !isset($instrument['id']) ||
            !$this->hasValidId($instrument['id'])
        ) {
            throw new Exception('Can not create instrument object. Missing valid MBID');
        }

        $this->brainz         = $brainz;
        $this->data           = $instrument;
        $this->id             = (string)$instrument['id'];
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

    /**
     * @return array{
     *     id: string,
     *     name: string,
     *     type-id: string,
     *     type: ?string,
     *     description: string,
     *     disambiguation: string
     *  }
     */
    public function getProps(bool $includeData = false): array
    {
        $results = [
            'id' => $this->id,
            'name' => $this->name,
            'type-id' => $this->type_id,
            'type' => $this->type,
            'description' => $this->description,
            'disambiguation' => $this->disambiguation,
        ];

        if ($includeData) {
            $results['data'] = $this->data;
        }

        return $results;
    }
}
