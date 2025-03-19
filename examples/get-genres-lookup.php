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
$client   = new Client($config);
$username = null;
$password = null;
$brainz   = new MusicBrainz(new GuzzleHttpAdapter($client), $username, $password);
$brainz->setUserAgent('ApplicationName', MusicBrainz::VERSION, 'https://example.com');

/**
 * Lookup an Artist and print their genres
 * @see http://musicbrainz.org/doc/Artist
 */
$includes = [
    'genres',
];
try {
    $lookup = $brainz->lookup('artist', '4dbf5678-7a31-406a-abbe-232f8ac2cd63', $includes);
    $artist = $brainz->getObject($lookup, 'artist');
    print_r("\n===========\n   ARTIST  \n===========\n");
    print_r($artist->getName() . "\n");
    print_r("===========\n   GENRES  \n===========\n");
    foreach ($brainz->getObjects($lookup, 'genre') as $genre) {
        print_r($genre->getName() . "\n");
    }
} catch (Exception $e) {
    print $e->getMessage();
    die();
}
print "\n\n";


/**
 * Lookup a Release Group and print out the genres and the mbid's of each item
 * @see http://musicbrainz.org/doc/Release_Group
 */
$includes = [
    'genres',
];
try {
    $lookup = $brainz->lookup('release-group', 'f05647bf-86a7-3e6e-872a-20eb465be0a5', $includes);
    $artist = $brainz->getObject($lookup, 'release-group');
    print_r("\n=============\n   RELEASE\n=============\n");
    print_r($artist->getName() . "\n" . $artist->getId() . "\n");
    print_r("============\n   GENRES\n============\n");
    foreach ($brainz->getObjects($lookup, 'genre') as $genre) {
        print_r($genre->getName() . " - " . $genre->getId() . "\n");
    }
} catch (Exception $e) {
    print $e->getMessage();
    die();
}
