# MusicBrainz Web Service (v2) PHP class

This PHP library that allows you to easily access the MusicBrainz Web Service V2 API. Visit the [MusicBrainz development page](http://musicbrainz.org/doc/Development) for more information.

This project is a fork of [chrisdawson/MusicBrainz](https://github.com/chrisdawson/MusicBrainz) and takes some inspiration from the [Python bindings](https://github.com/alastair/python-musicbrainz-ngs)

As of 2025-03-12 the project has been forked again from [mikealmond/MusicBrainz](https://github.com/mikealmond/MusicBrainz)

## Usage Example

```php
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
```

Look in the [/examples](https://github.com/mikealmond/MusicBrainz/tree/master/examples) folder for more.

## Requirements

PHP8.2+ and [cURL extension](http://php.net/manual/en/book.curl.php).

You must also choose a HTTP client to use.

They are not included in the composer requirements to allow you to choose your own based on your project requirements.

Add it to your composer requirements with composer require.

* The library is built to use
  * rmccue/requests
  * guzzlehttp/guzzle
  * guzzle/guzzle (deprecated)

## License

### Short

Use it in any project, no matter if it is commercial or not. Just don't remove the copyright notice.

### MIT License

Copyright © 2015 Mike Almond

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
