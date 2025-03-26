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

    /** @var LifeSpan[] $life_span */
    public ?array $life_span = null;

    /** @var Area[]|null $area */
    public ?array $area = null;

    /** @var null|Coordinate[] $coordinates */
    public ?array $coordinates;

    public string $disambiguation;

    private array $data;

    protected MusicBrainz $brainz;

    /**
     * @param array $place
     * @param MusicBrainz $brainz
     * @throws Exception
     */
    public function __construct(
        array $place,
        MusicBrainz $brainz,
    ) {
        if (
            !isset($place['id']) ||
            !$this->hasValidId($place['id'])
        ) {
            throw new Exception('Can not create place object. Missing valid MBID');
        }

        $this->brainz         = $brainz;
        $this->data           = $place;
        $this->id             = (string)$place['id'];
        $this->name           = (string)($place['name'] ?? '');
        $this->address        = (string)($place['address'] ?? '');
        $this->type_id        = (string)($place['type-id'] ?? '');
        $this->type           = (string)($place['type'] ?? '');
        $this->area           = $place['area'];
        $this->life_span      = $place['life-span'];
        $this->coordinates    = $place['coordinates'] ?? null;
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

    /**
     * @return array{
     *      id: string,
     *      name: string,
     *      address: string,
     *      type-id: string,
     *      type: string,
     *      area: ?Area[],
     *      life-span: ?LifeSpan[],
     *      coordinates: ?Coordinate[],
     *      disambiguation: string,
     *     data?: array<string, mixed>
     * }
     */
    public function getProps(bool $includeData = false): array
    {
        $results = [
            'id' => $this->id,
            'name' => $this->name,
            'address' => $this->address,
            'type-id' => $this->type_id,
            'type' => $this->type,
            'area' => $this->area,
            'life-span' => $this->life_span,
            'coordinates' => $this->coordinates,
            'disambiguation' => $this->disambiguation,
        ];

        if ($includeData) {
            $results['data'] = $this->data;
        }

        return $results;
    }
}
