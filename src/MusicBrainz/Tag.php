<?php

declare(strict_types=1);

namespace MusicBrainz;

/**
 * Represents a MusicBrainz tag object
 * @package MusicBrainz
 */
class Tag
{
    public string $name;

    public int $score;

    private array $data;

    private MusicBrainz $brainz;

    /**
     * @param array $tag
     * @param MusicBrainz $brainz
     */
    public function __construct(array $tag, MusicBrainz $brainz)
    {
        $this->data   = $tag;
        $this->brainz = $brainz;

        $this->name  = (string)($tag['name'] ?? '');
        $this->score = (int)($tag['score'] ?? 0);
    }
}
