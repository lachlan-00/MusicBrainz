<?php

declare(strict_types=1);

namespace MusicBrainz\Entities;

use MusicBrainz\Exception;
use MusicBrainz\MusicBrainz;

/**
 * Represents a MusicBrainz Recording object
 * @package MusicBrainz
 */
class Recording extends AbstractEntity implements EntityInterface
{
    public string $id;

    public string $title;

    public int $length;

    public int $score;

    public ?string $artistID = null;

    /** @var Release[] */
    public array $releases = [];

    private array $data;

    protected MusicBrainz $brainz;

    /**
     * @param array $recording
     * @param MusicBrainz $brainz
     * @throws Exception
     */
    public function __construct(
        array $recording,
        MusicBrainz $brainz
    ) {
        if (
            !isset($recording['id']) ||
            !$this->hasValidId($recording['id'])
        ) {
            throw new Exception('Can not create recording object. Missing valid MBID');
        }

        $this->brainz   = $brainz;
        $this->data     = $recording;
        $this->id       = (string)$recording['id'];
        $this->title    = (string)$recording['title'];
        $this->length   = (int)($recording['length'] ?? 0);
        $this->score    = (int)($recording['score'] ?? 0);
        $this->artistID = $recording['artistID'] ?? $recording['artist-credit'][0]['artist']['id'] ?? null;

        if (isset($recording['releases'])) {
            $this->setReleases($recording['releases']);
        }
    }

    /**
     * @param Release|array $releases
     * @return Recording
     */
    public function setReleases(array $releases): Recording
    {
        foreach ($releases as $release) {
            $this->releases[] = ($release instanceof Release)
                ? $release
                : new Release($release, $this->brainz);
        }

        return $this;
    }

    public function getScore(): int
    {
        return $this->score;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getReleaseDates(): array
    {

        if (empty($this->releases)) {
            throw new Exception('Could not find any releases in the recording');
        }

        $releaseDates = [];

        foreach ($this->releases as $release) {
            $releaseDates[] = $release->getReleaseDate();
        }

        asort($releaseDates);

        return $releaseDates;
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
        return $this->getName();
    }

    /**
     * @return Artist
     * @throws Exception
     */
    public function getArtist(): Artist
    {
        $includes = [
            'releases',
            'recordings',
            'release-groups',
        ];

        // don't get user data if we don't have user/pass
        if (
            $this->brainz->getPassword() !== null &&
            $this->brainz->getUser() !== null
        ) {
            $includes[] = 'user-ratings';
        }

        $artist = (array)$this->brainz->lookup('artist', $this->artistID, $includes);

        return new Artist($artist, $this->brainz);
    }

    /**
     * @param string $format
     *
     * @return int|string
     */
    public function getLength($format = 'int')
    {
        switch ($format) {
            case 'short':
                return str_replace('.', ':', number_format(($this->length / 1000 / 60), 2));
            case 'long':
                return str_replace('.', 'm ', number_format(($this->length / 1000 / 60), 2)) . 's';
            case 'int':
            default:
                return $this->length;
        }
    }
}
