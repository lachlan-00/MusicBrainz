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

    /** @var Attribute[] $attributes */
    public array $attributes;

    public string $disambiguation;

    private array $data;

    private MusicBrainz $brainz;

    /**
     * @param array $work
     * @param MusicBrainz $brainz
     * @throws Exception
     */
    public function __construct(
        array $work,
        MusicBrainz $brainz
    ) {
        if (
            !isset($work['id']) ||
            !$this->hasValidId($work['id'])
        ) {
            throw new Exception('Can not create work object. Missing valid MBID');
        }

        $this->brainz         = $brainz;
        $this->data           = $work;
        $this->id             = $work['id'];
        $this->title          = (string)($work['title'] ?? '');
        $this->type_id        = (string)($work['type-id'] ?? '');
        $this->type           = (string)($work['type'] ?? '');
        $this->language       = (string)($work['language'] ?? '');
        $this->languages      = $work['languages'];
        $this->iswcs          = $work['iswcs'];
        $this->attributes     = $work['attributes'];
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

    public function getTitle(): string
    {
        return $this->getName();
    }
}
