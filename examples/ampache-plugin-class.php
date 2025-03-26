<?php

use MusicBrainz\Entities\Artist;
use MusicBrainz\Entities\EntityInterface;
use MusicBrainz\Entities\Genre;
use MusicBrainz\Entities\Label;
use MusicBrainz\Entities\Recording;
use MusicBrainz\Entities\ReleaseGroup;
use MusicBrainz\MusicBrainz;
use MusicBrainz\Objects\LifeSpan;
use MusicBrainz\Objects\Tag;

require dirname(__DIR__) . '/vendor/autoload.php';

class AmpacheMusicBrainz
{
    private ?string $username;
    private ?string $password;
    private MusicBrainz $brainz;

    /**
     * @var array|array[]
     */
    public array $media_info;

    /**
     * @property array|array[] $media_objects
     */
    public array $media_objects;

    /**
     * Constructor
     * This function does nothing
     */
    public function __construct()
    {
        $this->username = null;
        $this->password = null;
        $this->brainz   = MusicBrainz::newMusicBrainz('request', $this->username, $this->password);
        $this->brainz->setUserAgent(
            'AmpacheMusicBrainz',
            MusicBrainz::VERSION,
            'https://ampache.org'
        );
        $this->media_info = [
            [
                'mb_trackid' => '140e8071-d7bb-4e05-9547-bfeea33916d0',
                'name' => 'The Shape',
                'artist' => 'Code 64',
            ],
            [
                'mb_albumid_group' => '299f707e-ddf1-4edc-8a76-b0e85a31095b',
                'name' => 'The Shape',
                'parent' => 'Code 64',
            ],
            [
                'mb_artistid' => '859a5c63-08df-42da-905c-7307f56db95d',
                'name' => 'Code 64',
            ],
            [
                'mb_labelid' => 'b66d15cc-b372-4dc1-8cbd-efdeb02e23e7',
                'name' => 'Arrow land',
            ],
        ];
        $this->media_objects = [
            [
                'object' => [
                    'mbid' => '140e8071-d7bb-4e05-9547-bfeea33916d0',
                    'name' => 'The Shape',
                ],
                'type' => 'song',
            ],
            [
                'object' => [
                    'mbid' => '299f707e-ddf1-4edc-8a76-b0e85a31095b',
                    'name' => 'The Shape',
                ],
                'type' => 'album',
            ],
            [
                'object' => [
                    'mbid' => '859a5c63-08df-42da-905c-7307f56db95d',
                    'name' => 'Code 64',
                ],
                'type' => 'artist',
            ],
            [
                'object' => [
                    'mbid' => 'b66d15cc-b372-4dc1-8cbd-efdeb02e23e7',
                    'name' => 'Arrow land',
                ],
                'type' => 'label',
            ],
        ];
    }

    private function debug_event(string $message): void
    {
        echo sprintf('%s: %s', 'MusicBrainz.plugin', $message) . "\n";
    }

    /**
     * find
     * Lookup item by mbid or search by name / artist information
     * @param array<string, string|null> $media_info
     */
    private function _find(array $media_info): ?EntityInterface
    {
        if (isset($media_info['mb_trackid'])) {
            $object_type = 'track';
            $mbid        = $media_info['mb_trackid'];
            $fullname    = $media_info['song'] ?? $media_info['title'] ?? '';
            $parent_name = $media_info['artist'] ?? '';
        } elseif (isset($media_info['mb_albumid_group'])) {
            $object_type = 'album';
            $mbid        = $media_info['mb_albumid_group'];
            $fullname    = $media_info['album'] ?? $media_info['title'] ?? '';
            $parent_name = $media_info['albumartist'] ?? $media_info['artist'] ?? '';
        } elseif (isset($media_info['mb_artistid'])) {
            $object_type = 'artist';
            $mbid        = $media_info['mb_artistid'];
            $fullname    = $media_info['artist'] ?? $media_info['title'] ?? '';
            $parent_name = '';
        } elseif (isset($media_info['mb_labelid'])) {
            $object_type = 'label';
            $mbid        = $media_info['mb_labelid'];
            $fullname    = $media_info['label'] ?? $media_info['title'] ?? '';
            $parent_name = '';
        } else {
            return null;
        }

        $results = false;
        if (MusicBrainz::isMBID($mbid)) {
            try {
                $brainz = MusicBrainz::newMusicBrainz('request');
                switch ($object_type) {
                    case 'label':
                        $lookup = $brainz->lookup($object_type, $mbid, ['genres', 'tags']);
                        /**
                         * https://musicbrainz.org/ws/2/label/b66d15cc-b372-4dc1-8cbd-efdeb02e23e7?fmt=json
                         * @var Label $results
                         */
                        $results = $brainz->getObject($lookup, $object_type);
                        break;
                    case 'album':
                        $lookup = $brainz->lookup('release-group', $mbid, ['releases', 'genres', 'tags']);
                        /**
                         * https://musicbrainz.org/ws/2/release-group/299f707e-ddf1-4edc-8a76-b0e85a31095b?inc=releases+tags&fmt=json
                         * @var ReleaseGroup $results
                         */
                        $results = $brainz->getObject($lookup, 'release-group');
                        break;
                    case 'artist':
                        $lookup = $brainz->lookup($object_type, $mbid, ['release-groups', 'genres', 'tags']);
                        /**
                         * https://musicbrainz.org/ws/2/artist/859a5c63-08df-42da-905c-7307f56db95d?inc=release-groups+tags&fmt=json
                         * @var Artist $results
                         */
                        $results = $brainz->getObject($lookup, $object_type);
                        break;
                    case 'track':
                        $lookup = $brainz->lookup('recording', $mbid, ['artists', 'releases', 'genres', 'tags']);
                        /**
                         * https://musicbrainz.org/ws/2/recording/140e8071-d7bb-4e05-9547-bfeea33916d0?inc=artists+releases&fmt=json
                         * @var Recording $results
                         */
                        $results = $brainz->getObject($lookup, 'recording');

                        break;
                    default:
                }
            } catch (Exception $error) {
                self::debug_event('Lookup error ' . $error->getMessage());

                return null;
            }
        } else {
            try {
                $brainz = MusicBrainz::newMusicBrainz('request');
                switch ($object_type) {
                    case 'label':
                        $args   = ['name' => $fullname];
                        $filter = MusicBrainz::newFilter('label', $args);
                        $search = $brainz->search($filter, 1, null, false);
                        /**
                         * https://musicbrainz.org/ws/2/label?query=Arrow%20land&fmt=json
                         * @var Label[] $results
                         */
                        $results = $brainz->getObjects($search, $object_type);
                        if (!empty($results)) {
                            /** @var Label $results */
                            $results = $results[0];
                        }

                        break;
                    case 'album':
                        $args = [
                            'release' => $fullname,
                            'artist' => $parent_name,
                        ];
                        $filter = MusicBrainz::newFilter('release-group', $args);
                        $search = (array)$brainz->search(
                            $filter,
                            1,
                            null,
                            false,
                        );
                        /**
                         * https://musicbrainz.org/ws/2/release-group?query=release:The%20Shape%20AND%20artist:Code%2064&fmt=json
                         * @var ReleaseGroup[] $results
                         */
                        $results = $brainz->getObjects($search, 'release-group');
                        if (!empty($results)) {
                            /** @var ReleaseGroup $results */
                            $results = $results[0];
                        }

                        break;
                    case 'artist':
                        $args   = ['name' => $fullname];
                        $filter = MusicBrainz::newFilter('artist', $args);
                        $search = (array)$brainz->search(
                            $filter,
                            1,
                            null,
                            false,
                        );
                        /**
                         * https://musicbrainz.org/ws/2/artist?query=name:Code%2064&fmt=json
                         * @var Artist[] $results
                         */
                        $results = $brainz->getObjects($search, 'artist');
                        if (!empty($results)) {
                            /** @var Artist $results */
                            $results = $results[0];
                        }

                        break;
                    case 'track':
                        $args = [
                            'title' => $fullname,
                            'artist' => $parent_name,
                        ];
                        $filter = MusicBrainz::newFilter('recording', $args);
                        $search = (array)$brainz->search(
                            $filter,
                            1,
                            null,
                            false,
                        );
                        /**
                         * https://musicbrainz.org/ws/2/release-group?query=release:The%20Shape%20AND%20artist:Code%2064&fmt=json
                         * @var Recording[] $results
                         */
                        $results = $brainz->getObjects($search, 'recording');
                        if (!empty($results)) {
                            /** @var Recording $results */
                            $results = $results[0];
                        }

                        break;
                    default:
                        return null;
                }
            } catch (Exception $error) {
                self::debug_event('Lookup error ' . $error);

                return null;
            }
        }

        // couldn't find an object
        if (!$results instanceof EntityInterface) {
            return null;
        }

        return $results;
    }

    /**
     * get_metadata
     * Returns song metadata for what we're passed in.
     */
    public function get_metadata(array $gather_types, array $media_info): array
    {
        // Music metadata only
        if (!in_array('music', $gather_types)) {
            return [];
        }
        try {
            $brainz = MusicBrainz::newMusicBrainz('request');
        } catch (Exception) {
            return [];
        }

        if (isset($media_info['mb_trackid'])) {
            $object_type = 'track';
        } elseif (isset($media_info['mb_albumid_group'])) {
            $object_type = 'album';
        } elseif (isset($media_info['mb_artistid'])) {
            $object_type = 'artist';
        } elseif (isset($media_info['mb_labelid'])) {
            $object_type = 'label';
        } else {
            return [];
        }

        // lookup a musicbrainz object
        $results = self::_find($media_info);

        // couldn't find an object
        if (!$results instanceof EntityInterface) {
            self::debug_event('Entity not found ' . $object_type);

            return [];
        }

        $genres     = [];
        $brainzData = $results->getData();
        try {
            foreach ($brainz->getObjects($brainzData, 'tag') as $tag) {
                /** @var Tag $tag */
                $genres[] = $tag->name;
            }
        } catch (Exception $error) {
            self::debug_event('Error getting tags ' . $error->getMessage());
        }
        try {
            foreach ($brainz->getObjects($brainzData, 'genre') as $genre) {
                /** @var Genre $genre */
                $genres[] = $genre->getName();
            }
        } catch (Exception $error) {
            self::debug_event('Error getting genres ' . $error->getMessage());
        }

        if (
            isset($brainzData['artist-credit']) ||
            isset($brainzData['releases'])
        ) {
            // pull first artist-credit
            if (isset($brainzData['artist-credit']) && count($brainzData['artist-credit']) > 0) {
                $artist = $brainzData['artist-credit'][0];
                $artist = (is_array($artist))
                    ? $artist['artist']
                    : (array)$artist->{'artist'};
            }

            // pull first release
            if (isset($brainzData['releases']) && count($brainzData['releases']) == 1) {
                $release = $brainzData['releases'][0];
            }

            $results = (array)$results;
            if (isset($artist)) {
                $results['mb_artistid'] = $artist['id'];
                $results['artist']      = $artist['name'];
            }

            if (isset($release)) {
                $results['album'] = is_array($release)
                    ? $release['title']
                    : $release->title;
            }
        } else {
            $results = (array)$results;
        }

        if (!empty($genres)) {
            $results['genre'] = array_unique($genres);
        }

        return $results;
    }

    /**
     * get_external_metadata
     * Update an object (label or artist for now) using musicbrainz
     */
    public function get_external_metadata($object, string $object_type): bool
    {
        // Artist and label metadata only for now
        $media_info = [];
        $fullname   = $object['name'];
        if ($object_type === 'song') {
            self::debug_event('get_external_metadata only supports Labels and Artists (' . $object_type . ')');

            return false;
        }
        if ($object_type === 'album') {
            self::debug_event('get_external_metadata only supports Labels and Artists (' . $object_type . ')');

            return false;
        }

        if ($object_type === 'artist') {
            $media_info['mb_artistid'] = $object['mbid'];
            $media_info['artist']      = $fullname;
            $results                   = self::_find($media_info);
        } elseif ($object_type === 'label') {
            $media_info['mb_labelid'] = $object['mbid'];
            $media_info['label']      = $fullname;
            $results                  = self::_find($media_info);
        } else {
            self::debug_event('get_external_metadata only supports Labels and Artists (' . $object_type . ')');

            return false;
        }

        if ($results instanceof EntityInterface) {
            try {
                self::debug_event(sprintf('Updating %s: ', $object_type) . $fullname);
                $data       = [];
                $brainzData = $results->getData();
                $life_span  = $brainzData['life-span'] ?? null;
                $active     = 1;
                $begin      = '';
                if (is_array($life_span)) {
                    $active = ($life_span['ended'] == 1) ? 0 : 1;
                    $begin  = $life_span['begin'] ?? '';
                } elseif (is_object($life_span)) {
                    /** @var LifeSpan $life_span */
                    $active = ($life_span->{'ended'} == 1) ? 0 : 1;
                    $begin  = $life_span->{'begin'} ?? '';
                }

                $begin_area = $brainzData['begin-area'] ?? null;
                $beginName  = null;
                if (is_array($begin_area)) {
                    $beginName = $begin_area['name'] ?? null;
                } elseif (is_object($begin_area)) {
                    $beginName = $begin_area->{'name'} ?? null;
                }

                $area     = $brainzData['area'] ?? null;
                $areaName = null;
                if (is_array($area)) {
                    $areaName = ($area['name']) ?? null;
                } elseif (is_object($area)) {
                    $areaName = ($area->{'name'}) ?? null;
                }
            } catch (Exception) {
                return false;
            }

            switch ($object_type) {
                case 'label':
                    $data = [
                        /** @var Label $results */
                        'name' => $results->getName(),
                        'mbid' => $results->getId(),
                        'category' => $results->type ?? 'original-category',
                        'summary' => $results->getData()['disambiguation'] ?? 'original-summary',
                        'address' => 'original-address',
                        'country' => $results->country ?? 'original-country',
                        'email' => 'original-email',
                        'website' => 'original-website',
                        'active' => $active
                    ];
                    break;
                case 'artist':
                    $data = [
                        'name' => $results->getName(),
                        'mbid' => $results->getId(),
                        'summary' => 'original-summary',
                        'placeformed' => $beginName ?? $areaName ?? null,
                        'yearformed' => explode('-', ($begin))[0] ?? 'original-yearformed'
                    ];
                    break;
            }

            if (!empty($data)) {
                self::debug_event(sprintf('Updating %s: ', $object_type) . $fullname);
            }

            return true;
        }

        return false;
    }

    /**
     * get_artist
     * Get an artist from musicbrainz
     */
    public function get_artist(string $mbid): array
    {
        //debug_event(self::class, "get_artist: {{$mbid}}", 4);
        $results = false;
        $data    = [];
        if (MusicBrainz::isMBID($mbid)) {
            try {
                $brainz = MusicBrainz::newMusicBrainz('request');
                $lookup = $brainz->lookup('artist', $mbid, ['tags']);
                /**
                 * https://musicbrainz.org/ws/2/artist/859a5c63-08df-42da-905c-7307f56db95d?inc=release-groups&fmt=json
                 * @var Artist $results
                 */
                $results = $brainz->getObject($lookup, 'artist');
            } catch (Exception $error) {
                self::debug_event('Lookup error ' . $error->getMessage());

                return [];
            }
        }

        if ($results) {
            $data = [
                'name' => $results->getName(),
                'mbid' => $results->getId(),
            ];
        }

        return $data;
    }
}
