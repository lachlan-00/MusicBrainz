<?php

declare(strict_types=1);

namespace MusicBrainz\Entities;

use MusicBrainz\Exception;
use MusicBrainz\MusicBrainz;

/**
 * Represents a MusicBrainz release group
 *
 */
class ReleaseGroup extends AbstractEntity implements EntityInterface
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
     * @throws Exception
     */
    public function __construct(
        array $releaseGroup,
        MusicBrainz $brainz,
    ) {
        if (
            !isset($releaseGroup['id']) ||
            !$this->hasValidId($releaseGroup['id'])
        ) {
            throw new Exception('Can not create release-group object. Missing valid MBID');
        }

        $this->brainz = $brainz;
        $this->data   = $releaseGroup;
        $this->id     = (string)$releaseGroup['id'];
        $this->title  = (string)($releaseGroup['title'] ?? '');
        $this->score  = (int)($releaseGroup['score'] ?? 0);
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

    /**
     * @return array{
     *     id: string,
     *     title: string,
     *     score: int
     * }
     */
    public function getProps(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'score' => $this->score,
        ];
    }

    public function getTitle(): string
    {
        return $this->getName();
    }

    public function getScore(): int
    {
        return $this->score;
    }

    /**
     * @return Release[]
     * @throws Exception
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
