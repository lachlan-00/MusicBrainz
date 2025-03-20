<?php

declare(strict_types=1);

namespace MusicBrainz\Entities;

use MusicBrainz\Exception;
use MusicBrainz\MusicBrainz;
use MusicBrainz\Objects\LifeSpan;

/**
 * Represents a MusicBrainz event object
 * @package MusicBrainz
 */
class Event extends AbstractEntity implements EntityInterface
{
    public string $id;

    public string $name;

    public ?string $type = null;

    public string $type_id;

    public string $time;

    public string $setlist;

    public string $disambiguation;

    public bool $cancelled;

    public LifeSpan $life_span;

    private array $data;

    private MusicBrainz $brainz;

    /**
     * @param array $event
     * @param MusicBrainz $brainz
     * @throws Exception
     */
    public function __construct(
        array $event,
        MusicBrainz $brainz,
    ) {
        if (
            !isset($event['id']) ||
            !$this->hasValidId($event['id'])
        ) {
            throw new Exception('Can not create event object. Missing valid MBID');
        }

        $this->brainz         = $brainz;
        $this->data           = $event;
        $this->id             = $event['id'];
        $this->name           = (string)($event['name'] ?? '');
        $this->type_id        = (string)($event['type-id'] ?? '');
        $this->type           = (string)($event['type'] ?? '');
        $this->time           = (string)($event['time'] ?? '');
        $this->setlist        = (string)($event['setlist'] ?? '');
        $this->life_span      = $event['life-span'];
        $this->disambiguation = (string)($event['disambiguation'] ?? '');
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
