<pre><?php

use MusicBrainz\Entities\Recording;
use MusicBrainz\MusicBrainz;

require dirname(__DIR__) . '/vendor/autoload.php';

// Create new MusicBrainz object
$config = [
    'allow_redirects' => true,
    'verify' => false,
];

$username = null;
$password = null;
$brainz   = MusicBrainz::newMusicBrainz('guzzle', $username, $password, null, $config);
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
        MusicBrainz::newFilter('place', $args),
        1,
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
        MusicBrainz::newFilter('work', $args),
        1,
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
        MusicBrainz::newFilter('release-group', $args),
        1,
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
        MusicBrainz::newFilter('artist', $args),
        1,
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
        MusicBrainz::newFilter('recording', $args),
        1,
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
        MusicBrainz::newFilter('label', $args),
        1,
    );
    print_r($search);
} catch (Exception $e) {
    print $e->getMessage();
    die();
}
