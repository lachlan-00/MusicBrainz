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
 * Browse Releases based on an Artist MBID (Weezer in this case)
 * Include the Labels for the Release and the Recordings in it
 */
$includes = [];
try {
    $browse = $brainz->browseRecording(
        'artist',
        '6fe07aa5-fec0-4eca-a456-f29bff451b04',
        $includes,
        1,
    );

    foreach ($brainz->getObjects($browse, 'recording') as $recording) {
        print_r($recording->getData());
    }
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
        1,
    );

    foreach ($brainz->getObjects($browse, 'release') as $release) {
        print_r($release->getData());
    }
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
        1,
    );

    foreach ($brainz->getObjects($browse, 'release-group') as $releaseGroup) {
        print_r($releaseGroup->getData());
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
        1,
    );

    foreach ($brainz->getObjects($browse, 'artist') as $artist) {
        // print Artist data property
        print_r($artist->getData());
        foreach ($brainz->getObjects($artist->getData(), 'genre') as $genre) {
            // print each genre for the artist
            print_r($genre->getData());
        }
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
        1,
    );

    foreach ($brainz->getObjects($browse, 'label') as $label) {
        print_r($label->getData());
    }
} catch (Exception $e) {
    print $e->getMessage();
    die();
}
