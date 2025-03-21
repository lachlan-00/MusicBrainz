<?php

declare(strict_types=1);

namespace MusicBrainz\Objects;

/**
 * Represents a MusicBrainz tag object
 * @package MusicBrainz
 */
class Tag implements ObjectInterface
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
        array $tag,
    ) {
        $this->name  = (string)($tag['name'] ?? '');
        $this->count = (int)($tag['count'] ?? 0);
    }

    /**
     * Get the object properties as an array
     *
     * @return array{
     *     name: string,
     *     count: int
     * }
     */
    public function getProps(): array
    {
        return [
            'name' => $this->name,
            'count' => $this->count
        ];
    }
}
