<?php

declare(strict_types=1);

namespace MusicBrainz\Objects;

/**
 * Represents a MusicBrainz tag object
 * @package MusicBrainz
 */
class LifeSpan implements ObjectInterface
{
    public bool $ended;

    public ?string $end = null;

    public ?string $begin = null;

    /**
     * @param array{
     *     ended: ?bool,
     *     end: ?string,
     *     begin: ?string
     * } $tag
     */
    public function __construct(
        array $tag
    ) {
        $this->ended = (bool)($tag['ended'] ?? false);
        $this->end   = (string)($tag['end'] ?? '');
        $this->begin = (string)($tag['begin'] ?? '');
    }

    public function getData(): LifeSpan
    {
        return $this;
    }

    /**
     * Get the object properties as an array
     *
     * @return array{
     *     ended: bool,
     *     end: ?string,
     *     begin: ?string
     * }
     */
    public function getProps(): array
    {
        return [
            'ended' => $this->ended,
            'end' => $this->end,
            'begin' => $this->begin
        ];
    }
}
