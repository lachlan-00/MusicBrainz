<?php

use MusicBrainz\MusicBrainz;

require dirname(__DIR__) . '/vendor/autoload.php';

// Create Guzzle HTTP client config (if you want)
$config = [
    'allow_redirects' => true,
    'verify' => false,
];

// Some areas require authentication
$username = 'username';
$password = 'password';
// Create new MusicBrainz object
$brainz = MusicBrainz::newMusicBrainz('guzzle', $username, $password, null, $config);
$brainz->setUserAgent('ApplicationName', MusicBrainz::VERSION, 'https://example.com');

try {
    // Search for recordings and then return a list of Recording objects
    $args = [
        'recording' => 'Buddy Holly',
        'artist' => 'Weezer',
        'creditname' => 'Weezer',
        'status' => 'Official'
    ];

    /**
     * Search will return objects by default which are arrays of data
     * @var array $recordings
     */
    $recordings = $brainz->search(
        MusicBrainz::newFilter('recording', $args)
    );
    foreach ($recordings as $recording) {
        print('RECORDING ' . $recording->getId() . "\n");
        print_r($recording->getData());
    }
} catch (Exception $e) {
    print $e->getMessage();
    die();
}


try {
    // Look up the artist for a recording and print the Artist object data
    $includes = [
        'aliases',
        'ratings',
        'genres'
    ];

    $browse = $brainz->browseArtist(
        'recording',
        'd615590b-1546-441d-9703-b3cf88487cbd',
        $includes,
        1
    );
    foreach ($brainz->getObjects($browse, 'artist') as $artist) {
        print('ARTIST ' . $artist->getId() . "\n");
        print_r($artist->getData());
    }
} catch (Exception $e) {
    print $e->getMessage();
    die();
}
