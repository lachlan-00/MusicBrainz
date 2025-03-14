<?php

declare(strict_types=1);

namespace MusicBrainz\Entities;

use MusicBrainz\MusicBrainz;

/**
 * Represents a MusicBrainz release group
 *
 */
class ReleaseGroup implements EntityInterface
{
    public string $id;

    public string $title;

    public int $score;

    private array $data;

    private MusicBrainz $brainz;

    /** @var Release[] */
    private array $releases = [];

    /**
     * @param array $releaseGroup
     * @param MusicBrainz $brainz
     */
    public function __construct(
        array $releaseGroup,
        MusicBrainz $brainz
    ) {
        $this->data   = $releaseGroup;
        $this->brainz = $brainz;

        $this->id    = (string)($releaseGroup['id'] ?? '');
        $this->title = (string)($releaseGroup['title'] ?? '');
        $this->score = (int)($releaseGroup['score'] ?? 0);
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
        return self::getName();
    }

    public function getScore(): int
    {
        return $this->score;
    }

    /**
     * @return Release[]
     */
    public function getReleases(): array
    {
        if (!empty($this->releases)) {
            return $this->releases;
        }

        foreach ($this->data['releases'] as $release) {
            $this->releases[] = new Release($release, $this->brainz);
        }

        return $this->releases;
    }
}
