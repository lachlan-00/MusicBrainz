<pre><?php

use GuzzleHttp\Client;
use MusicBrainz\HttpAdapters\GuzzleHttpAdapter;
use MusicBrainz\MusicBrainz;

require dirname(__DIR__) . '/vendor/autoload.php';

// Create new MusicBrainz object
$config = [
    'allow_redirects' => true,
    'verify' => false,
];
$client = new Client($config);
$brainz = new MusicBrainz(new GuzzleHttpAdapter($client));
$brainz->setUserAgent('ApplicationName', MusicBrainz::VERSION, 'https://example.com');


/**
 * Browse Releases based on an Artist MBID (Weezer in this case)
 * Include the Labels for the Release and the Recordings in it
 */
$includes = ['labels', 'recordings'];
try {
    $details = $brainz->browseRelease(
        'artist',
        '6fe07aa5-fec0-4eca-a456-f29bff451b04',
        $includes,
        1
    );
    print_r($details);
} catch (Exception $e) {
    print $e->getMessage();
}
print "\n\n";


/**
 * Browse an artist based on a Recording MBID and include their aliases and ratings
 */
$includes = ['aliases', 'ratings', 'genres'];
try {
    $details = $brainz->browseArtist(
        'recording',
        'd615590b-1546-441d-9703-b3cf88487cbd',
        $includes,
        1
    );
    print_r($details);
} catch (Exception $e) {
    print $e->getMessage();
}
print "\n\n";


/**
 * Browse information for a Label based on a Release's MBID
 */
$includes = ['aliases'];
try {
    $details = $brainz->browseLabel(
        'release',
        '5a90bd38-62b6-46f5-9c39-cfceba169019',
        $includes,
        1
    );
    print_r($details);
} catch (Exception $e) {
    print $e->getMessage();
}
