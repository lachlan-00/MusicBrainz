<?php

declare(strict_types=1);

namespace MusicBrainz;

use MusicBrainz\HttpAdapters\AbstractHttpAdapter;
use OutOfBoundsException;

/**
 * Connect to the MusicBrainz web service
 *
 * http://musicbrainz.org/doc/Development
 *
 * @link http://github.com/lachlan-00/musicbrainz
 * @package MusicBrainz
 */
class MusicBrainz
{
    public const VERSION = '0.3.2';

    private const MBID_REGEX = '/^(\{)?[a-f\d]{8}(-[a-f\d]{4}){4}[a-f\d]{8}(?(1)})$/i';

    /** @var string[]> ENTITIES */
    private const ENTITIES = [
        'annotation', // TODO annotation MusicBrainz\Entities\Annotation
        'area',
        'artist',
        'collection',
        'discid', // TODO discid MusicBrainz\Entities\Discid
        'echoprint', // TODO echoprint MusicBrainz\Entities\Echoprint
        'event',
        'genre',
        'instrument',
        'isrc', // TODO isrc MusicBrainz\Entities\Isrc
        'iswc', // TODO iswc MusicBrainz\Entities\Iswc
        'label',
        'place',
        'puid', // TODO puid MusicBrainz\Entities\Puid
        'recording',
        'release-group',
        'release',
        'series',
        'url',
        'work',
    ];

    /** @var string[] $validReleaseTypes */
    private static array $validReleaseTypes = [
        'album',
        'audiobook',
        'compilation',
        'ep',
        'interview',
        'live',
        'nat',
        'other',
        'remix',
        'single',
        'soundtrack',
        'spokenword',
    ];

    /** @var string[] $validReleaseStatuses */
    private static array $validReleaseStatuses = [
        'bootleg',
        'official',
        'promotion',
        'pseudo-release',
    ];

    private string $userAgent;

    private ?string $user = null; // The username a MusicBrainz user. Used for authentication.

    private ?string $password = null; // The password of a MusicBrainz user. Used for authentication.

    private AbstractHttpAdapter $adapter; // The Http adapter used to make requests

    /**
     * Initializes the class. You can pass the user’s username and password
     * However, you can modify or add all values later.
     */
    public function __construct(
        AbstractHttpAdapter $adapter,
        ?string $user = null,
        ?string $password = null
    ) {
        $this->adapter   = $adapter;
        $this->userAgent = 'MusicBrainz PHP Api/' . self::VERSION;

        if (!empty($user)) {
            $this->setUser($user);
        }

        if (!empty($password)) {
            $this->setPassword($password);
        }
    }

    /**
     * Do a MusicBrainz lookup
     *
     * http://musicbrainz.org/doc/XML_Web_Service
     *
     * @param string $entity
     * @param string $mbid MusicBrainz ID
     * @param array $includes
     *
     * @return array|object
     * @throws Exception
     */
    public function lookup(
        string $entity,
        string $mbid,
        array $includes = []
    ): array|object {
        if (!$this->_isValidEntity($entity)) {
            throw new Exception('Invalid entity');
        }

        match ($entity) {
            'area', 'annotation', 'event', 'genre', 'instrument', 'place', 'series', 'url' => $this->validateInclude($includes, [], $entity),
            'artist' => $this->validateInclude($includes, Filters\ArtistFilter::INCLUDES, $entity),
            'collection' => $this->validateInclude($includes, [
                    'user-collections',
                    'releases',
                ], $entity),
            'discid' => $this->validateInclude($includes, [
                    'artist-credits',
                    'artist-rels',
                    'artists',
                    'discids',
                    'echoprints',
                    'isrcs',
                    'label-rels',
                    'labels',
                    'media',
                    'puids',
                    'recording-level-rels',
                    'recording-rels',
                    'recordings',
                    'release-group-rels',
                    'release-groups',
                    'release-rels',
                    'url-rels',
                    'work-level-rels',
                    'work-rels',
                ], $entity),
            'echoprint' => $this->validateInclude($includes, [
                    'artists',
                    'releases',
                ], $entity),
            'isrc', 'puid' => $this->validateInclude($includes, [
                    'artists',
                    'echoprints',
                    'isrcs',
                    'puids',
                    'releases',
                ], $entity),
            'iswc' => $this->validateInclude($includes, [
                    'artists',
                    'collection',
                ], $entity),
            'label' => $this->validateInclude($includes, Filters\LabelFilter::INCLUDES, $entity),
            'recording' => $this->validateInclude($includes, Filters\RecordingFilter::INCLUDES, $entity),
            'release' => $this->validateInclude($includes, Filters\ReleaseFilter::INCLUDES, $entity),
            'release-group' => $this->validateInclude($includes, Filters\ReleaseGroupFilter::INCLUDES, $entity),
            'work' => $this->validateInclude($includes, [
                    'aliases',
                    'annotation',
                    'artist-rels',
                    'artists', // sub queries
                    'label-rels',
                    'ratings',
                    'recording-rels',
                    'release-group-rels',
                    'release-rels',
                    'tags',
                    'url-rels',
                    'user-ratings', // misc
                    'user-tags',
                    'work-rels',
                ], $entity),
            default => throw new Exception('Invalid entity')
        };

        $authRequired = $this->isAuthRequired($entity, $includes);

        $params = [
            'inc' => implode('+', $includes),
            'fmt' => 'json'
        ];

        return $this->adapter->call($entity . '/' . $mbid, $params, $this->getHttpOptions(), $authRequired);
    }

    /**
     * @param Filters\FilterInterface $filter
     * @param string $entity
     * @param string $mbid
     * @param array $includes
     * @param int $limit
     * @param null|int $offset
     * @param array $releaseType
     * @param array $releaseStatus
     *
     * @return array
     * @throws Exception
     */
    protected function browse(
        Filters\FilterInterface $filter,
        string $entity,
        string $mbid,
        array $includes,
        int $limit = 25,
        ?int $offset = null,
        array $releaseType = [],
        array $releaseStatus = []
    ): array {
        if (!$this->isValidMBID($mbid)) {
            throw new Exception('Invalid Music Brainz ID');
        }

        if ($limit > 100) {
            throw new Exception('Limit can only be between 1 and 100');
        }

        $this->validateInclude($includes, $filter->getIncludes(), $filter->getEntity());

        $authRequired = $this->isAuthRequired($filter->getEntity(), $includes);

        $params = $this->getBrowseFilterParams($filter->getEntity(), $includes, $releaseType, $releaseStatus);
        $params += [
            $entity => $mbid,
            'inc' => implode('+', $includes),
            'limit' => $limit,
            'offset' => $offset,
            'fmt' => 'json'
        ];

        return (array)$this->adapter->call($filter->getEntity() . '/', $params, $this->getHttpOptions(), $authRequired);
    }

    /**
     * @param string $entity
     * @param string $mbid
     * @param array $includes
     * @param int $limit
     * @param null|int $offset
     *
     * @return array
     * @throws Exception
     */
    public function browseArtist(
        string $entity,
        string $mbid,
        array $includes = [],
        int $limit = 25,
        ?int $offset = null
    ): array {
        $filter = new Filters\ArtistFilter([]);
        if (!$filter->hasLink($entity)) {
            throw new Exception('Invalid browse entity for artist: ' . $entity);
        }

        return $this->browse($filter, $entity, $mbid, $includes, $limit, $offset);
    }

    /**
     * @param string $entity
     * @param string $mbid
     * @param array $includes
     * @param int $limit
     * @param null|int $offset
     *
     * @return array
     * @throws Exception
     */
    public function browseLabel(
        string $entity,
        string $mbid,
        array $includes,
        int $limit = 25,
        ?int $offset = null
    ): array {
        $filter = new Filters\LabelFilter([]);
        if (!$filter->hasLink($entity)) {
            throw new Exception('Invalid browse entity for label: ' . $entity);
        }

        return $this->browse($filter, $entity, $mbid, $includes, $limit, $offset);
    }

    /**
     * @param string $entity
     * @param string $mbid
     * @param array $includes
     * @param int $limit
     * @param null|int $offset
     *
     * @return array
     * @throws Exception
     */
    public function browseRecording(
        string $entity,
        string $mbid,
        array $includes = [],
        int $limit = 25,
        ?int $offset = null
    ): array {
        $filter = new Filters\RecordingFilter([]);
        if (!$filter->hasLink($entity)) {
            throw new Exception('Invalid browse entity for recording: ' . $entity);
        }

        return $this->browse($filter, $entity, $mbid, $includes, $limit, $offset);
    }

    /**
     * @param string $entity
     * @param string $mbid
     * @param array $includes
     * @param int $limit
     * @param null|int $offset
     * @param array $releaseType
     * @param array $releaseStatus
     *
     * @return array
     * @throws Exception
     */
    public function browseRelease(
        string $entity,
        string $mbid,
        array $includes = [],
        int $limit = 25,
        ?int $offset = null,
        array $releaseType = [],
        array $releaseStatus = []
    ): array {
        $filter = new Filters\ReleaseFilter([]);
        if (!$filter->hasLink($entity)) {
            throw new Exception('Invalid browse entity for release: ' . $entity);
        }

        return $this->browse(
            $filter,
            $entity,
            $mbid,
            $includes,
            $limit,
            $offset,
            $releaseType,
            $releaseStatus
        );
    }

    /**
     * @param string $entity
     * @param string $mbid
     * @param int $limit
     * @param null|int $offset
     * @param array $includes
     * @param array $releaseType
     *
     * @return array
     * @throws Exception
     */
    public function browseReleaseGroup(
        string $entity,
        string $mbid,
        int $limit = 25,
        ?int $offset = null,
        array $includes = [],
        array $releaseType = []
    ): array {
        $filter = new Filters\ReleaseGroupFilter([]);
        if (!$filter->hasLink($entity)) {
            throw new Exception('Invalid browse entity for release-group: ' . $entity);
        }

        return $this->browse(
            $filter,
            $entity,
            $mbid,
            $includes,
            $limit,
            $offset,
            $releaseType
        );
    }

    /**
     * Performs a query based on the parameters supplied in the Filter object.
     * Returns an array of possible matches with scores, as returned by the
     * musicBrainz web service.
     *
     * Note that these types of queries only return some information, and not all the
     * information available about a particular item is available using this type of query.
     * You will need to get the MusicBrainz id (mbid) and perform a lookup with browse
     * to return complete information about a release. This method returns an array of
     * objects that are possible matches.
     *
     * @param Filters\FilterInterface $filter
     * @param int $limit
     * @param null|int $offset
     * @param boolean $parseResponse parse the results array or simply return the result
     *
     * @return array|object
     * @throws Exception
     */
    public function search(
        Filters\FilterInterface $filter,
        int $limit = 25,
        ?int $offset = null,
        bool $parseResponse = true
    ): array|object {
        if (count($filter->createParameters()) < 1) {
            throw new Exception('The search filter object needs at least 1 argument to create a query.');
        }

        if ($limit > 100) {
            throw new Exception('Limit can only be between 1 and 100');
        }

        $params = $filter->createParameters(['limit' => $limit, 'offset' => $offset, 'fmt' => 'json']);

        $response = $this->adapter->call($filter->getEntity() . '/', $params, $this->getHttpOptions(), false, true);

        if (
            $parseResponse &&
            is_array($response)
        ) {
            return $filter->parseResponse($response, $this);
        }

        return $response;
    }

    public function isValidMBID(?string $mbid): bool
    {
        return self::isMBID((string)$mbid);
    }

    /**
     * Public function to check if a string is a valid MusicBrainz ID
     */
    public static function isMBID(string $mbid = ''): bool
    {
        return (bool)preg_match(self::MBID_REGEX, $mbid);
    }

    /**
     * Check the list of allowed entities
     */
    private function _isValidEntity(string $entity): bool
    {
        return array_key_exists($entity, self::ENTITIES);
    }

    /**
     * Some calls require authentication
     */
    protected function isAuthRequired(
        string $entity,
        array $includes
    ): bool {
        if (
            in_array('user-tags', $includes) ||
            in_array('user-ratings', $includes)
        ) {
            return true;
        }

        if (str_starts_with($entity, 'collection')) {
            return true;
        }

        return false;
    }

    /**
     * @param array $includes
     * @param array $validIncludes
     *
     * @return bool
     * @throws OutOfBoundsException
     */
    public function validateInclude(
        array $includes,
        array $validIncludes,
        string $entity
    ): bool {
        foreach ($includes as $include) {
            if (!in_array($include, $validIncludes)) {
                throw new OutOfBoundsException(sprintf('%s is not a valid include for %s', $include, $entity));
            }
        }

        return true;
    }

    /**
     * @param array $values
     * @param array $valid
     *
     * @return bool
     * @throws Exception
     */
    public function validateFilter(
        array $values,
        array $valid
    ): bool {
        foreach ($values as $value) {
            if (!in_array($value, $valid)) {
                throw new Exception(sprintf('%s is not a valid filter', $value));
            }
        }

        return true;
    }

    /**
     * Check that the status or type values are valid. Then, check that
     * the filters can be used with the given includes.
     *
     * @param string $entity
     * @param array $includes
     * @param array $releaseType
     * @param array $releaseStatus
     *
     * @return array
     * @throws Exception
     */
    public function getBrowseFilterParams(
        string $entity,
        array $includes,
        array $releaseType = [],
        array $releaseStatus = []
    ): array {
        //$this->validateFilter(array($entity), self::ENTITY_INCLUDES);
        $this->validateFilter($releaseStatus, self::$validReleaseStatuses);
        $this->validateFilter($releaseType, self::$validReleaseTypes);

        if (!empty($releaseStatus)
            && !in_array('releases', $includes)
        ) {
            throw new Exception('Can\'t have a status with no release include');
        }

        if (!empty($releaseType)
            && !in_array('release-groups', $includes)
            && !in_array('releases', $includes)
            && $entity != 'release-group'
        ) {
            throw new Exception('Can\'t have a release type with no release-group include');
        }

        $params = [];

        if (!empty($releaseType)) {
            $params['type'] = implode('|', $releaseType);
        }

        if (!empty($releaseStatus)) {
            $params['status'] = implode('|', $releaseStatus);
        }

        return $params;
    }

    /**
     * @return array{method: string, user-agent: string, user: ?string, password: ?string}
     */
    public function getHttpOptions(): array
    {
        return [
            'method' => 'GET',
            'user-agent' => $this->getUserAgent(),
            'user' => $this->getUser(),
            'password' => $this->getPassword()
        ];
    }

    /**
     * Returns the user agent.
     */
    public function getUserAgent(): string
    {
        return $this->userAgent;
    }

    /**
     * Set the user agent for POST requests (and GET requests for user tags)
     *
     * @param string $application The name of the application using this library
     * @param string $version The version of the application using this library
     * @param string $contactInfo E-mail or website of the application
     *
     * @throws Exception
     */
    public function setUserAgent(
        string $application,
        string $version,
        string $contactInfo
    ): void {
        if (str_contains($version, '-')) {
            throw new Exception('User agent: version should not contain a \'-\' character.');
        }

        $this->userAgent = $application . '/' . $version . ' (' . $contactInfo . ')';
    }

    /**
     * Returns the MusicBrainz user
     */
    public function getUser(): ?string
    {
        return $this->user;
    }

    /**
     * Sets the MusicBrainz user
     */
    public function setUser(string $user): void
    {
        $this->user = $user;
    }

    /**
     * Returns the user’s password
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * Sets the user’s password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }
}
