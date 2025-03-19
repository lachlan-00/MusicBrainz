<?php

declare(strict_types=1);

namespace MusicBrainz\Entities;

use MusicBrainz\Exception;
use MusicBrainz\MusicBrainz;

/**
 * Represents a MusicBrainz collection object
 * @package MusicBrainz
 */
class Collection extends AbstractEntity implements EntityInterface
{
    public string $id;

    public string $name;

    public string $type;

    public string $type_id;

    public string $editor;

    public int $event_count;

    private array $data;

    private MusicBrainz $brainz;

    /**
     * @param array $collection
     * @param MusicBrainz $brainz
     * @throws Exception
     */
    public function __construct(
        array $collection,
        MusicBrainz $brainz
    ) {
        if (
            !isset($collection['id']) ||
            !$this->hasValidId($collection['id'])
        ) {
            throw new Exception('Can not create collection object. Missing valid MBID');
        }

        $this->brainz      = $brainz;
        $this->data        = $collection;
        $this->id          = $collection['id'];
        $this->name        = (string)($collection['name'] ?? '');
        $this->type        = (string)($collection['type'] ?? '');
        $this->type_id     = (string)($collection['type-id'] ?? '');
        $this->editor      = (string)($collection['editor'] ?? '');
        $this->event_count = (int)($collection['event-count'] ?? 0);
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
