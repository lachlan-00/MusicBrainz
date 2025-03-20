<?php

declare(strict_types=1);

namespace MusicBrainz;

use GuzzleHttp\Client;
use Guzzle\Http\Client as OldClient;
use MusicBrainz\Entities\Area;
use MusicBrainz\Entities\Artist;
use MusicBrainz\Entities\Collection;
use MusicBrainz\Entities\DiscId;
use MusicBrainz\Entities\EntityInterface;
use MusicBrainz\Entities\Event;
use MusicBrainz\Entities\Genre;
use MusicBrainz\Entities\Instrument;
use MusicBrainz\Entities\Label;
use MusicBrainz\Entities\Place;
use MusicBrainz\Entities\Recording;
use MusicBrainz\Entities\Release;
use MusicBrainz\Entities\ReleaseGroup;
use MusicBrainz\Entities\Series;
use MusicBrainz\Entities\Url;
use MusicBrainz\Entities\Work;
use MusicBrainz\Filters\FilterInterface;
use MusicBrainz\HttpAdapters\AbstractHttpAdapter;
use MusicBrainz\Objects\Annotation;
use MusicBrainz\Objects\Attribute;
use MusicBrainz\Objects\Coordinate;
use MusicBrainz\Objects\LifeSpan;
use MusicBrainz\Objects\Tag;
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
    public const VERSION = '0.4.0';

    private const MBID_REGEX = '/^(\{)?[a-f\d]{8}(-[a-f\d]{4}){4}[a-f\d]{8}(?(1)})$/i';

    /** @var string[] $ENTITIES */
    private const ENTITIES = [
        'annotation',
        'area',
        'artist',
        'collection',
        'discid',
        'echoprint', // TODO echoprint MusicBrainz\Objects\Echoprint
        'event',
        'genre',
        'instrument',
        'isrc', // String objects
        'iswc', // String objects
        'label',
        'place',
        'recording',
        'release-group',
        'release',
        'series',
        'url',
        'work',
    ];

    /**
     * https://musicbrainz.org/doc/Release_Group/Type
     * @var string[] $RELEASE_TYPE
     */
    private const RELEASE_TYPE = [
        'album',
        'audiobook',
        'audio drama',
        'broadcast',
        'compilation',
        'demo',
        'dj-mix',
        'ep',
        'field recording',
        'interview',
        'live',
        'mixtape/street',
        'nat',
        'other',
        'remix',
        'single',
        'soundtrack',
        'spokenword',
    ];

    /**
     * https://wiki.musicbrainz.org/Style/Release#Status
     * @var string[] RELEASE_STATUS
     */
    private const RELEASE_STATUS = [
        'bootleg',
        'cancelled',
        'expunged',
        'official',
        'promotion',
        'pseudo-release',
        'withdrawn',
    ];

    public const DEFAULT_INCLUDES = [
        'area-rels',
        'artist-rels',
        'event-rels',
        'genre-rels',
        'instrument-rels',
        'label-rels',
        'place-rels',
        'recording-rels',
        'release-rels',
        'release-group-rels',
        'series-rels',
        'url-rels',
        'work-rels',
    ];

    private string $userAgent;

    private ?string $user = null; // The username a MusicBrainz user. Used for authentication.

    private ?string $password = null; // The password of a MusicBrainz user. Used for authentication.

    private AbstractHttpAdapter $adapter; // The Http adapter used to make requests

    private ?FilterInterface $filter = null; // Result filter used to get output objects from requests

    /**
     * Initializes the class. You can pass the user’s username and password
     * However, you can modify or add all values later.
     */
    public function __construct(
        AbstractHttpAdapter $adapter,
        ?string $user = null,
        ?string $password = null,
        ?FilterInterface $filter = null
    ) {
        $this->adapter   = $adapter;
        $this->filter    = $filter;
        $this->userAgent = 'MusicBrainz PHP Api/' . self::VERSION;

        if (!empty($user)) {
            $this->setUser($user);
        }

        if (!empty($password)) {
            $this->setPassword($password);
        }
    }

    /**
     * Create a new MusicBrainz object without having to create a new Http adapter
     */
    public static function newMusicBrainz(
        string $adapter,
        ?string $user = null,
        ?string $password = null,
        ?FilterInterface $filter = null,
        array $guzzleOptions = [],
    ): MusicBrainz {
        $adapter = match ($adapter) {
            'guzzle' => new HttpAdapters\GuzzleHttpAdapter(new Client($guzzleOptions)),
            'guzzle-old' => new HttpAdapters\GuzzleHttpOldAdapter(new OldClient('', $guzzleOptions)),
            'requests' => new HttpAdapters\RequestsHttpAdapter(),
            default => throw new Exception('Invalid http adapter')
        };

        return new MusicBrainz($adapter, $user, $password, $filter);
    }


    /**
     * Create a new MusicBrainz object without having to create a new Http adapter
     */
    public static function newFilter(
        string $filterName,
        array $args = [],
    ): ?FilterInterface {
        return match ($filterName) {
            'area' => self::newFilter('area', $args),
            'artist' => self::newFilter('artist', $args),
            'collection' => self::newFilter('collection', $args),
            'discid' => self::newFilter('discid', $args),
            'echoprint' => self::newFilter('echoprint', $args),
            'genre' => self::newFilter('genre', $args),
            'isrc' => self::newFilter('isrc', $args),
            'iswc' => self::newFilter('iswc', $args),
            'label' => self::newFilter('label', $args),
            'place' => self::newFilter('place', $args),
            'recording' => self::newFilter('recording', $args),
            'release' => self::newFilter('release', $args),
            'release-group', 'releasegroup' => self::newFilter('release-group', $args),
            'tag' => self::newFilter('tag', $args),
            'work' => self::newFilter('work', $args),
            default => throw new Exception('Invalid filter type'),
        };
    }


    /**
     * Check the list of allowed entities
     */
    private function _isValidEntity(string $entity): bool
    {
        return in_array($entity, self::ENTITIES);
    }

    /**
     * Some calls require authentication
     */
    private function _isAuthRequired(
        string $entity,
        array $includes,
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
        array $includes = [],
    ): array|object {
        if (!$this->_isValidEntity($entity)) {
            throw new Exception('Invalid entity');
        }

        match ($entity) {
            'annotation', 'event', 'genre', 'instrument', 'place', 'series', 'url' => $this->validateInclude($includes, self::DEFAULT_INCLUDES, $entity),
            'area' => $this->validateInclude($includes, Filters\AreaFilter::INCLUDES, $entity),
            'artist' => $this->validateInclude($includes, Filters\ArtistFilter::INCLUDES, $entity),
            'collection' => $this->validateInclude($includes, Filters\CollectionFilter::INCLUDES, $entity),
            'discid' => $this->validateInclude($includes, Filters\DiscIdFilter::INCLUDES, $entity),
            'echoprint' => $this->validateInclude($includes, Filters\EchoPrintFilter::INCLUDES, $entity),
            'isrc' => $this->validateInclude($includes, Filters\IsrcFilter::INCLUDES, $entity),
            'iswc' => $this->validateInclude($includes, Filters\IswcFilter::INCLUDES, $entity),
            'label' => $this->validateInclude($includes, Filters\LabelFilter::INCLUDES, $entity),
            'recording' => $this->validateInclude($includes, Filters\RecordingFilter::INCLUDES, $entity),
            'release' => $this->validateInclude($includes, Filters\ReleaseFilter::INCLUDES, $entity),
            'release-group', 'releasegroup' => $this->validateInclude($includes, Filters\ReleaseGroupFilter::INCLUDES, $entity),
            'work' => $this->validateInclude($includes, Filters\WorkFilter::INCLUDES, $entity),
            default => throw new Exception('Invalid entity')
        };

        $authRequired = $this->_isAuthRequired($entity, $includes);

        $params = [
            'inc' => implode('+', $includes),
            'fmt' => 'json',
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
    public function browse(
        Filters\FilterInterface $filter,
        string $entity,
        string $mbid,
        array $includes,
        int $limit = 25,
        ?int $offset = null,
        array $releaseType = [],
        array $releaseStatus = [],
    ): array {
        if (!$this->isValidMBID($mbid)) {
            throw new Exception('Invalid Music Brainz ID');
        }

        if ($limit > 100) {
            throw new Exception('Limit can only be between 1 and 100');
        }

        $this->validateInclude($includes, $filter->getIncludes(), $filter->getEntity());

        $authRequired = $this->_isAuthRequired($filter->getEntity(), $includes);

        $params = $this->getBrowseFilterParams($filter->getEntity(), $includes, $releaseType, $releaseStatus);
        $params += [
            $entity => $mbid,
            'inc' => implode('+', $includes),
            'limit' => $limit,
            'offset' => $offset,
            'fmt' => 'json',
        ];

        return (array)$this->adapter->call($filter->getEntity() . '/', $params, $this->getHttpOptions(), $authRequired);
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
        bool $parseResponse = true,
    ): array|object {
        if (count($filter->createParameters()) < 1) {
            throw new Exception('The search filter object needs at least 1 argument to create a query.');
        }
        if (!$filter->canSearch()) {
            throw new Exception(sprintf('The filter object %s does not support search queries.', $filter->getEntity()));
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
            return self::getObjects($response, $filter->getEntity());
        }

        return $response;
    }

    public function getObjects(
        array|object $response,
        ?string $filterName = null,
    ): array {
        if ($filterName !== null) {
            $this->setFilterByString($filterName);
        }

        $filter = $this->getFilter();
        if ($filter === null) {
            throw new Exception('No filter set');
        }

        return $filter->parseResponse((array)$response, $this);
    }

    /**
     * Parse the response from the web service and return an object based on what you're expecting
     * @throws Exception
     */
    public function getObject(
        array|object $response,
        ?string $filterName = null,
    ): EntityInterface {
        return match ($filterName) {
            'annotation' => new Annotation((array)$response),
            'area' => new Area((array)$response, $this),
            'artist' => new Artist((array)$response, $this),
            'attribute' => new Attribute((array)$response),
            'collection' => new Collection((array)$response, $this),
            'coordinate' => new Coordinate((array)$response),
            'discid' => new DiscId((array)$response, $this),
            'event' => new Event((array)$response, $this),
            'genre' => new Genre((array)$response, $this),
            'instrument' => new Instrument((array)$response, $this),
            'label' => new Label((array)$response, $this),
            'life-span' => new LifeSpan((array)$response),
            'place' => new Place((array)$response, $this),
            'recording' => new Recording((array)$response, $this),
            'release' => new Release((array)$response, $this),
            'release-group', 'releasegroup' => new ReleaseGroup((array)$response, $this),
            'tag' => new Tag((array)$response),
            'series' => new Series((array)$response, $this),
            'url' => new Url((array)$response, $this),
            'work' => new Work((array)$response, $this),
            default => throw new Exception('Invalid filter name')
        };
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
        ?int $offset = null,
    ): array {
        $filter = new Filters\ArtistFilter();
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
    public function browseCollection(
        string $entity,
        string $mbid,
        array $includes,
        int $limit = 25,
        ?int $offset = null,
    ): array {
        $filter = new Filters\CollectionFilter();
        if (!$filter->hasLink($entity)) {
            throw new Exception('Invalid browse entity for collection: ' . $entity);
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
        ?int $offset = null,
    ): array {
        $filter = new Filters\LabelFilter();
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
        ?int $offset = null,
    ): array {
        $filter = new Filters\RecordingFilter();
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
        array $releaseStatus = [],
    ): array {
        $filter = new Filters\ReleaseFilter();
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
     * @param array $includes
     * @param int $limit
     * @param null|int $offset
     * @param array $releaseType
     *
     * @return array
     * @throws Exception
     */
    public function browseReleaseGroup(
        string $entity,
        string $mbid,
        array $includes = [],
        int $limit = 25,
        ?int $offset = null,
        array $releaseType = [],
    ): array {
        $filter = new Filters\ReleaseGroupFilter();
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
     * @param array $includes
     * @param array $validIncludes
     *
     * @return bool
     * @throws OutOfBoundsException
     */
    public function validateInclude(
        array $includes,
        array $validIncludes,
        string $entity,
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
        array $valid,
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
        array $releaseStatus = [],
    ): array {
        //$this->validateFilter(array($entity), self::ENTITY_INCLUDES);
        $this->validateFilter($releaseStatus, self::RELEASE_STATUS);
        $this->validateFilter($releaseType, self::RELEASE_TYPE);

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
        string $contactInfo,
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

    /**
     * Returns the current output filter
     */
    public function getFilter(): ?FilterInterface
    {
        return $this->filter;
    }

    /**
     * Sets the output filter
     */
    public function setFilter(?FilterInterface $filter): void
    {
        $this->filter = $filter;
    }
    /**
     * Sets the output filter
     * @param string $filterName
     * @param string[]|null $args
     */
    public function setFilterByString(string $filterName, ?array $args = null): void
    {
        match ($filterName) {
            'area' => $this->setFilter(new Filters\AreaFilter($args)),
            'artist' => $this->setFilter(new Filters\ArtistFilter($args)),
            'collection' => $this->setFilter(new Filters\CollectionFilter($args)),
            'discid' => $this->setFilter(new Filters\DiscIdFilter($args)),
            'echoprint' => $this->setFilter(new Filters\EchoPrintFilter($args)),
            'genre' => $this->setFilter(new Filters\GenreFilter($args)),
            'isrc' => $this->setFilter(new Filters\IsrcFilter($args)),
            'iswc' => $this->setFilter(new Filters\IswcFilter($args)),
            'label' => $this->setFilter(new Filters\LabelFilter($args)),
            'place' => $this->setFilter(new Filters\PlaceFilter($args)),
            'recording' => $this->setFilter(new Filters\RecordingFilter($args)),
            'release' => $this->setFilter(new Filters\ReleaseFilter($args)),
            'release-group', 'releasegroup' => $this->setFilter(new Filters\ReleaseGroupFilter($args)),
            'tag' => $this->setFilter(new Filters\TagFilter($args)),
            'work' => $this->setFilter(new Filters\WorkFilter($args)),
        };
    }
}
