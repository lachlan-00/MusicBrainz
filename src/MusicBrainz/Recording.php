<?php

namespace MusicBrainz;

/**
 * Represents a MusicBrainz Recording object
 * @package MusicBrainz
 */
class Recording
{
    public string $id;

    public string $title;

    public int $length;

    public int $score;

    public string $artistID;

    /** @var Release[] */
    public array $releases = [];

    private array $data;

    protected MusicBrainz $brainz;

    /**
     * @param array       $recording
     * @param MusicBrainz $brainz
     */
    public function __construct(array $recording, MusicBrainz $brainz)
    {
        $this->data   = $recording;
        $this->brainz = $brainz;

        $this->id       = (string)$recording['id'];
        $this->title    = (string)$recording['title'];
        $this->length   = (int)($recording['length'] ?? 0);
        $this->score    = (int)($recording['score'] ?? 0);
        $this->artistID = $recording['artist-credit'][0]['artist']['id'];

        if (isset($recording['releases'])) {
            $this->setReleases($recording['releases']);
        }
    }

    /**
     * @param array $releases
     * @return Recording
     */
    public function setReleases(array $releases): Recording
    {
        foreach ($releases as $release) {
            $this->releases[] = new Release($release, $this->brainz);
        }

        return $this;
    }

    public function getScore(): int
    {
        return $this->score;
    }

    /**
     * @throws Exception
     * @return array
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
            'user-ratings'
        ];

        $artist = $this->brainz->lookup('artist', $this->artistID, $includes);

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
