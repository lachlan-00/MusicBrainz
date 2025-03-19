<?php

declare(strict_types=1);

namespace MusicBrainz\Objects;

use MusicBrainz\MusicBrainz;

/**
 * Represents a MusicBrainz tag object
 * @package MusicBrainz
 */
class LifeSpan
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
}
