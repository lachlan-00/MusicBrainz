<pre><?php

use GuzzleHttp\Client;
use MusicBrainz\Entities\Artist;
use MusicBrainz\Entities\ReleaseGroup;
use MusicBrainz\HttpAdapters\GuzzleHttpAdapter;
use MusicBrainz\MusicBrainz;

require dirname(__DIR__) . '/vendor/autoload.php';

// Create new MusicBrainz object
$config = [
    'allow_redirects' => true,
    'verify' => false,
];
$client   = new Client($config);
$username = null;
$password = null;
$brainz   = new MusicBrainz(new GuzzleHttpAdapter($client), $username, $password);
$brainz->setUserAgent('ApplicationName', MusicBrainz::VERSION, 'https://example.com');

/**
 * Browse Releases based on an Artist MBID (Weezer in this case)
 * Include the Labels for the Release and the Recordings in it
 */
$includes = [];
try {
    $browse = $brainz->browseRecording(
        'artist',
        '6fe07aa5-fec0-4eca-a456-f29bff451b04',
        $includes,
        1
    );
    print_r($browse);
} catch (Exception $e) {
    print $e->getMessage();
    die();
}
print "\n\n";


/**
 * Browse Releases based on an Artist MBID (Weezer in this case)
 * Include the Labels for the Release and the Recordings in it
 */
$includes = ['labels', 'recordings'];
try {
    $browse = $brainz->browseRelease(
        'artist',
        '6fe07aa5-fec0-4eca-a456-f29bff451b04',
        $includes,
        1
    );
    print_r($browse);
} catch (Exception $e) {
    print $e->getMessage();
    die();
}
print "\n\n";


/**
 * Browse ReleaseGroups based on an Artist MBID (Weezer in this case)
 * Include the Labels for the Release and the Recordings in it
 */
$includes = ['genres', 'artist-credits'];
try {
    $browse = $brainz->browseReleaseGroup(
        'artist',
        '6fe07aa5-fec0-4eca-a456-f29bff451b04',
        $includes,
        1
    );
    foreach ($browse['release-groups'] as $releaseGroups) {
        $object = new ReleaseGroup((array)$releaseGroups, $brainz);
        print_r($object->getData());
    }
} catch (Exception $e) {
    print $e->getMessage();
    die();
}
print "\n\n";


/**
 * Browse an artist based on a Recording MBID and include their aliases and ratings
 */
$includes = ['aliases', 'ratings', 'genres'];
try {
    $browse = $brainz->browseArtist(
        'recording',
        'd615590b-1546-441d-9703-b3cf88487cbd',
        $includes,
        1
    );
    foreach ($browse['artists'] as $artist) {
        $object = new Artist((array)$artist, $brainz);
        print_r($object->getData());
    }
} catch (Exception $e) {
    print $e->getMessage();
    die();
}
print "\n\n";


/**
 * Browse information for a Label based on a Release's MBID
 */
$includes = ['aliases'];
try {
    $browse = $brainz->browseLabel(
        'release',
        '5a90bd38-62b6-46f5-9c39-cfceba169019',
        $includes,
        1
    );
    print_r($browse);
} catch (Exception $e) {
    print $e->getMessage();
    die();
}
