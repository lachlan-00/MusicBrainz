<?php

declare(strict_types=1);

namespace MusicBrainz;

/**
 * Represents a MusicBrainz artist object
 * @package MusicBrainz
 */
class Artist
{
    public string $id;

    public string $name;

    private string $type;

    private string $sortName;

    private string $gender;

    private string $country;

    private ?string $beginDate = null;

    private ?string $endDate = null;

    private ?array $releases = null;

    private array $data;

    protected MusicBrainz $brainz;

    /**
     * @param array $artist
     * @param MusicBrainz $brainz
     *
     * @throws Exception
     */
    public function __construct(
        array $artist,
        MusicBrainz $brainz
    ) {
        if (
            !isset($artist['id']) ||
            !$brainz->isValidMBID($artist['id'])
        ) {
            throw new Exception('Can not create artist object. Missing valid MBID');
        }

        $this->data   = $artist;
        $this->brainz = $brainz;

        $this->id        = (string)$artist['id'];
        $this->name      = (string)($artist['name'] ?? '');
        $this->type      = (string)($artist['type'] ?? '');
        $this->sortName  = (string)($artist['sort-name'] ?? '');
        $this->gender    = (string)($artist['gender'] ?? '');
        $this->country   = (string)($artist['country'] ?? '');
        $this->beginDate = $artist['life-span']['begin'] ?? null;
        $this->endDate   = $artist['life-span']['ended'] ?? null;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getSortName(): string
    {
        return $this->sortName;
    }

    public function getGender(): string
    {
        return $this->gender;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function getBeginDate(): ?string
    {
        return $this->beginDate;
    }

    public function getEndDate(): ?string
    {
        return $this->endDate;
    }

    public function getScore(): int
    {
        return (int)($this->data['score'] ?? 0);
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getReleases(): array
    {
        if (null === $this->releases) {
            $this->releases = $this->brainz->browseRelease('artist', $this->getId());
        }

        return $this->releases;
    }

    public function getId(): string
    {
        return $this->id;
    }
}
