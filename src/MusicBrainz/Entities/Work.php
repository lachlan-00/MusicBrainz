<?php

declare(strict_types=1);

namespace MusicBrainz\Entities;

use MusicBrainz\Exception;
use MusicBrainz\MusicBrainz;
use MusicBrainz\Objects\Attribute;

/**
 * Represents a MusicBrainz work object
 * @package MusicBrainz
 */
class Work extends AbstractEntity implements EntityInterface
{
    public string $id;

    public string $title;

    public string $type_id;

    public string $type;

    public string $language;

    /** @var string[] $languages */
    public array $languages;

    /** @var string[] $iswcs */
    public array $iswcs;

    /** @var null|Attribute[] $attributes */
    public ?array $attributes = null;

    public string $disambiguation;

    private array $data;

    protected MusicBrainz $brainz;

    /**
     * @param array $work
     * @param MusicBrainz $brainz
     * @throws Exception
     */
    public function __construct(
        array $work,
        MusicBrainz $brainz,
    ) {
        if (
            !isset($work['id']) ||
            !$this->hasValidId($work['id'])
        ) {
            throw new Exception('Can not create work object. Missing valid MBID');
        }

        $this->brainz         = $brainz;
        $this->data           = $work;
        $this->id             = (string)$work['id'];
        $this->title          = (string)($work['title'] ?? '');
        $this->type_id        = (string)($work['type-id'] ?? '');
        $this->type           = (string)($work['type'] ?? '');
        $this->language       = (string)($work['language'] ?? '');
        $this->languages      = $work['languages'];
        $this->iswcs          = $work['iswcs'] ?? [];
        $this->attributes     = $work['attributes'] ?? null;
        $this->disambiguation = (string)($work['disambiguation'] ?? '');
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->title;
    }

    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @return array{
     *     id: string,
     *     title: string,
     *     type_id: string,
     *     type: string,
     *     language: string,
     *     languages: string[],
     *     iswcs: string[],
     *     attributes: Attribute[]|null,
     *     disambiguation: string,
     *     data?: array<string, mixed>
     * }
     */
    public function getProps(bool $includeData = false): array
    {
        $results = [
            'id' => $this->id,
            'title' => $this->title,
            'type_id' => $this->type_id,
            'type' => $this->type,
            'language' => $this->language,
            'languages' => $this->languages,
            'iswcs' => $this->iswcs,
            'attributes' => $this->attributes,
            'disambiguation' => $this->disambiguation,
        ];

        if ($includeData) {
            $results['data'] = $this->data;
        }

        return $results;
    }

    public function getTitle(): string
    {
        return $this->getName();
    }
}
