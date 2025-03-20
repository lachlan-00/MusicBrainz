<?php

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
 * Lookup an Area
 * @see http://musicbrainz.org/doc/Area
 */
$includes = [];
try {
    $lookup = $brainz->lookup('area', '85752fda-13c4-31a3-bee5-0e5cb1f51dad', $includes);
    print_r($lookup);
} catch (Exception $e) {
    print $e->getMessage();
    die();
}
print "\n\n";
/**
 * Lookup a discid
 * @see http://musicbrainz.org/doc/DiscId
 */
$includes = [];
try {
    $lookup = $brainz->lookup('discid', 'lwHl8fGzJyLXQR33ug60E8jhf4k-', $includes);
    print_r($lookup);
} catch (Exception $e) {
    print $e->getMessage();
    die();
}
print "\n\n";
/**
 * Lookup an event
 * @see http://musicbrainz.org/doc/Event
 */
$includes = MusicBrainz::DEFAULT_INCLUDES;
try {
    $lookup = $brainz->lookup('event', '53bc3923-73f8-4e43-8bd6-1edf96e1b070', $includes);
    print_r($lookup);
} catch (Exception $e) {
    print $e->getMessage();
    die();
}
print "\n\n";


/**
 * Lookup an genre
 * @see http://musicbrainz.org/doc/Genre
 */
$includes = [];
try {
    $lookup = $brainz->lookup('genre', 'ceeaa283-5d7b-4202-8d1d-e25d116b2a18', $includes);
    print_r($lookup);
} catch (Exception $e) {
    print $e->getMessage();
    die();
}
print "\n\n";


/**
 * Lookup an instrument
 * @see http://musicbrainz.org/doc/Instrument
 */
$includes = MusicBrainz::DEFAULT_INCLUDES;
try {
    $lookup = $brainz->lookup('instrument', '540280f1-d6cf-46bf-968b-695e99e216d7', $includes);
    print_r($lookup);
} catch (Exception $e) {
    print $e->getMessage();
    die();
}
print "\n\n";


/**
 * Lookup a place
 * @see http://musicbrainz.org/doc/Place
 */
$includes = MusicBrainz::DEFAULT_INCLUDES;
try {
    $lookup = $brainz->lookup('place', 'd10077c1-03d9-4fbb-a038-6e35ada8eb9d', $includes);
    print_r($lookup);
} catch (Exception $e) {
    print $e->getMessage();
    die();
}
print "\n\n";


/**
 * Lookup a series
 * @see http://musicbrainz.org/doc/Series
 */
$includes = MusicBrainz::DEFAULT_INCLUDES;
try {
    $lookup = $brainz->lookup('series', 'a7b6abe0-42fd-46dc-ae9c-c4345c9080dc', $includes);
    print_r($lookup);
} catch (Exception $e) {
    print $e->getMessage();
    die();
}
print "\n\n";


/**
 * Lookup a url
 * @see http://musicbrainz.org/doc/Url
 */
$includes = MusicBrainz::DEFAULT_INCLUDES;
try {
    $lookup = $brainz->lookup('url', 'aa48bac9-96f9-4776-9a09-9b125717ff63', $includes);
    print_r($lookup);
} catch (Exception $e) {
    print $e->getMessage();
    die();
}
print "\n\n";


/**
 * Lookup a work
 * @see http://musicbrainz.org/doc/Work
 */
$includes = [];
try {
    $lookup = $brainz->lookup('work', 'd3344072-c45c-4d3b-a114-005276f250ba', $includes);
    print_r($lookup);
} catch (Exception $e) {
    print $e->getMessage();
    die();
}
print "\n\n";


/**
 * Lookup an Artist and include a list of Releases, Recordings, Release Groups
 * @see http://musicbrainz.org/doc/Artist
 */
$includes = [
    'recordings',
    'releases',
    'release-groups',
    'works',
];
try {
    $lookup = $brainz->lookup('artist', '4dbf5678-7a31-406a-abbe-232f8ac2cd63', $includes);
    print_r($lookup);
} catch (Exception $e) {
    print $e->getMessage();
    die();
}
print "\n\n";


/**
 * Lookup a Label and include a list of Releases
 * @see http://musicbrainz.org/doc/Label
 */
$includes = [
    'releases',
];
try {
    $lookup = $brainz->lookup('label', 'b66d15cc-b372-4dc1-8cbd-efdeb02e23e7', $includes);
    print_r($lookup);
} catch (Exception $e) {
    print $e->getMessage();
    die();
}
print "\n\n";


/**
 * Lookup a Release Group based on an MBID
 * @see http://musicbrainz.org/doc/Release_Group
 */
$includes = ['artists', 'releases'];
try {
    // born this way: the remix
    $lookup = $brainz->lookup('release-group', 'e4307c5f-1959-4163-b4b1-ded4f9d786b0', $includes);
    print_r($lookup);
} catch (Exception $e) {
    print $e->getMessage();
    die();
}
print "\n\n";


/**
 * Lookup a Release based on an MBID
 * @see http://musicbrainz.org/doc/Release
 */
$includes = ['artists', 'release-groups'];
try {
    $lookup = $brainz->lookup('release', 'd8de198d-2162-4264-9cfe-926d92c4c7ad', $includes);
    print_r($lookup);
} catch (Exception $e) {
    print $e->getMessage();
    die();
}
