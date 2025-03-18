<pre><?php

use GuzzleHttp\Client;
use MusicBrainz\Entities\Recording;
use MusicBrainz\Filters\ArtistFilter;
use MusicBrainz\Filters\LabelFilter;
use MusicBrainz\Filters\PlaceFilter;
use MusicBrainz\Filters\RecordingFilter;
use MusicBrainz\Filters\ReleaseGroupFilter;
use MusicBrainz\Filters\WorkFilter;
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
 * Get the release groups for an artist
 * @see http://musicbrainz.org/doc/Release_Group
 */
$args = [
    'name' => 'Altern'
];
try {
    $search = $brainz->search(
        new PlaceFilter($args),
        1
    );
    print_r($search);
} catch (Exception $e) {
    print $e->getMessage();
    die();
}
print "\n\n";


/**
 * Get the release groups for an artist
 * @see http://musicbrainz.org/doc/Release_Group
 */
$args = [
    'title' => 'My Name is Jonas'
];
try {
    $search = $brainz->search(
        new WorkFilter($args),
        1
    );
    print_r($search);
} catch (Exception $e) {
    print $e->getMessage();
    die();
}
print "\n\n";


/**
 * Get the release groups for an artist
 * @see http://musicbrainz.org/doc/Release_Group
 */
$args = [
    'artist' => 'Weezer'
];
try {
    $search = $brainz->search(
        new ReleaseGroupFilter($args),
        1
    );
    print_r($search);
} catch (Exception $e) {
    print $e->getMessage();
    die();
}
print "\n\n";


/**
 * Do an artist search and return a list of artists that match
 * a search
 * @see http://musicbrainz.org/doc/Artist
 */
$args = [
    'artist' => 'Weezer'
];
try {
    $search = $brainz->search(
        new ArtistFilter($args),
        1
    );
    print_r($search);
} catch (Exception $e) {
    print $e->getMessage();
    die();
}
print "\n\n";


/**
 * Do a recording (song) search
 * @see http://musicbrainz.org/doc/Recording
 */
$args = [
    'recording' => 'Buddy Holly',
    'artist' => 'Weezer',
    'creditname' => 'Weezer',
    'status' => 'Official'
];
try {
    $search = $brainz->search(
        new RecordingFilter($args),
        1
    );
    foreach ($search as $recording) {
        $object = new Recording((array)$recording, $brainz);
        print_r($object->getData());
    }
} catch (Exception $e) {
    print $e->getMessage();
    die();
}
print "\n\n";


/**
 * Do a search for a label
 * @see http://musicbrainz.org/doc/Label
 */
$args = [
    'label' => 'Devils'
];
try {
    $search = $brainz->search(
        new LabelFilter($args),
        1
    );
    print_r($search);
} catch (Exception $e) {
    print $e->getMessage();
    die();
}
