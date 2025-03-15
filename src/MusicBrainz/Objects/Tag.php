<?php

declare(strict_types=1);

namespace MusicBrainz\Objects;

use MusicBrainz\MusicBrainz;

/**
 * Represents a MusicBrainz tag object
 * @package MusicBrainz
 */
class Tag
{
    public string $name;

    public int $count;

    /**
     * @param array{
     *     name: ?string,
     *     count: ?string
     * } $tag
     */
    public function __construct(
        array $tag
    ) {
        $this->name  = (string)($tag['name'] ?? '');
        $this->count = (int)($tag['count'] ?? 0);
    }
}
