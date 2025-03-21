<?php

require 'ampache-plugin-class.php';

// Create an instance of the AmpacheMusicBrainz class
$ampacheMusicBrainz = new AmpacheMusicBrainz();

foreach ($ampacheMusicBrainz->media_objects as $media) {
    echo "\nrun get_external_metadata for \n";
    print_r($media);
    echo "\n\n";

    // Call the get_release function with a specific MBID
    $releaseData = $ampacheMusicBrainz->get_external_metadata($media['object'], $media['type']);

    // Print the result
    print_r($releaseData);

    echo "\n\n";
}
echo "\nGet artist by MBID\n";

// Call the get_artist function with a specific MBID
$artistData = $ampacheMusicBrainz->get_artist('859a5c63-08df-42da-905c-7307f56db95d');

// Print the result
print_r($artistData);

echo "\n\n";

foreach ($ampacheMusicBrainz->media_info as $media) {
    echo "\nrun get_metadata for \n";
    print_r($media);
    echo "\n\n";

    // Call the get_release function with a specific MBID
    $releaseData = $ampacheMusicBrainz->get_metadata(['music'], $media);

    // Print the result
    print_r($releaseData);

    echo "\n\n";
}
