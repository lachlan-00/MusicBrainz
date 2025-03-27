<?php

declare(strict_types=1);

namespace MusicBrainz\Entities;

use MusicBrainz\Exception;
use MusicBrainz\MusicBrainz;

/**
 * Represents a MusicBrainz artist object
 * @package MusicBrainz
 */
class Artist extends AbstractEntity implements EntityInterface
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
     * @throws Exception
     */
    public function __construct(
        array $artist,
        MusicBrainz $brainz,
    ) {
        if (
            !isset($artist['id']) ||
            !$this->hasValidId($artist['id'])
        ) {
            throw new Exception('Can not create artist object. Missing valid MBID');
        }

        $this->brainz    = $brainz;
        $this->data      = $artist;
        $this->id        = (string)$artist['id'];
        $this->name      = (string)($artist['name'] ?? '');
        $this->type      = (string)($artist['type'] ?? '');
        $this->sortName  = (string)($artist['sort-name'] ?? '');
        $this->gender    = (string)($artist['gender'] ?? '');
        $this->country   = (string)($artist['country'] ?? '');
        if ((isset($artist['life-span']))) {
            if (is_array($artist['life-span'])) {
                $this->beginDate = $artist['life-span']['begin'] ?: null;
            } else {
                $this->beginDate = $artist['life-span']->{'begin'} ?: null;
            }
            if (is_array($artist['life-span'])) {
                $this->endDate = $artist['life-span']['end'] ?: null;
            } else {
                $this->endDate = $artist['life-span']->{'end'} ?: null;
            }
        }
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

    /**
     * @return array{
     *     id: string,
     *     name: string,
     *     type: string,
     *     sort-name: string,
     *     gender: string,
     *     country: string,
     *     begin-date: ?string,
     *     end-date: ?string,
     *     data?: array<string, mixed>
     * }
     */
    public function getProps(bool $includeData = false): array
    {
        $results = [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'sort-name' => $this->sortName,
            'gender' => $this->gender,
            'country' => $this->country,
            'begin-date' => $this->beginDate,
            'end-date' => $this->endDate,
        ];

        if ($includeData) {
            $results['data'] = $this->data;
        }

        return $results;
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
}
