<?php

declare(strict_types=1);

namespace MusicBrainz\Objects;

/**
 * Represents a MusicBrainz annotation object
 * @package MusicBrainz
 */
class Annotation implements ObjectInterface
{
    public string $entity;

    public string $type;

    public int $score;

    public string $text;

    /**
     * @param array{
     *     entity: string,
     *     type: string,
     *     score: ?int,
     *     text: ?string
     *  } $annotation
     */
    public function __construct(
        array $annotation
    ) {
        $this->entity = $annotation['entity'];
        $this->type   = $annotation['type'];
        $this->score  = (int)($annotation['score'] ?? 0);
        $this->text   = $annotation['text'] ?? '';
    }

    public function getData(): Annotation
    {
        return $this;
    }

    /**
     * Get the object properties as an array
     *
     * @return array{
     *     entity: string,
     *     type: string,
     *     score: int,
     *     text: string
     * }
     */
    public function getProps(): array
    {
        return [
            'entity' => $this->entity,
            'type' => $this->type,
            'score' => $this->score,
            'text' => $this->text
        ];
    }
}
