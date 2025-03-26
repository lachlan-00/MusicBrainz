# MusicBrainz

## x.x.x

### Added

* Add an option to Entity `getData` calls to include the data property in results
* Add `getData` to ObjectInterface to match Entity classes
* Updated Ampache example to use `getProps`

### Changed

* Make MusicBrainz a protected property on Entity classes

## 0.6.0

### Added

* Add Ampache examples
* Include `/objects` in rector config
* Added `getProps()` function to Entity and Object classes. (return class properties)
* New class `Object\Alias`
* Add `ObjectInterface` class

### Changed

* Don't include `/test` or `/examples` in release zip using `.gitattributes`

### Fixed

* Reading life-span in Artist
* Null life-span in Event, Place
* Null alias in Label
* Null Area in Place
* Missing includes (`name`, `title`) for ArtistFilter, Labelfilter, ReleaseFilter and ReleaseGroupFilter
* RecordingFilter typo for `releases`

## 0.5.0

Added functions so you don't have to call other classes outside the MusicBrainz class

Provide more options to simplify the identification and access of response data

More examples have been updated to show how to use these new methods.

### Added

* Add function `MusicBrainz::newMusicBrainz()` to create a new MusicBrainz object without importing the http classes
* Add function `MusicBrainz::newFilter()` to create a new Filter object without importing the filter classes
* Add function `MusicBrainz->setFilterByString()` to set an object filter without having to call the classes
* Add function `MusicBrainz->setFilter()` so you can store the filter object in the MusicBrainz class
* Add function `MusicBrainz->getFilter()` so you don't need to import more classes
* Add function `MusicBrainz->getObjects()` Get the sub-entities from a browse response / object
* Add function `MusicBrainz->getObject()` cast a data response into the matching Entity or Object class
* Add property `filter` to MusicBrainz class allowing you to store and use a filter object

### Changed

* Make browse a public method to allow outside class usage
* Change composer suggestion from the old `guzzle/guzzle` to the new package `guzzlehttp/guzzle`

### Fixed

* Query parameters for search being mangled with URL encoding
* Empty query parameters being overwritten with an empty array

## 0.4.0

### Added

* Updated Guzzle to v7. (Based on [mikealmond/MusicBrainz/pull/29](https://github.com/mikealmond/MusicBrainz/pull/29))
* GitHub QA checks
* Add `AbstractEntity` and `EntityInterface` classes. (Data objects are Entities that do not have an MBID)
* Add common functions to abstract classes
* Exception when searching by invalid entity types
* Missing Entities
  * Area
  * DiscId
  * Event
  * Genre
  * Instrument
  * Place
  * Series
  * Url
  * Work
* Missing data Objects
  * Annotation
  * Attribute
  * Coordinate
  * LifeSpan
* Missing typed parameters and properties
* Use class constants for common data strings where possible

### Changed

* Combine Include relationship lists instead of validating everything. (Allow mistakes and return the errors instead)
* Moved GuzzleHttp 3.8 to GuzzleHttpOld class
* Moved MusicBrainz Entity objects to the Entities folder
* Moved MusicBrainz Data objects to the Objects folder
* Move some of the larger data arrays to their Entity classes
* Require `Entity::getName()` for Entity objects. (`getTitle()` is still available where the object has a title property)

### Removed

* References to `puid`. (Removed from MusicBrainz database)
* Travis build yml

### Fixed

* Updated examples to use new GuzzleHttp version
* Missing Entity links for filtering by object
* Broken areas from typed parameters in 0.3.x

## 0.3.2

### Added

* Public function for validity checking `MusicBrainz::isMBID()`
* Checks for array/object in a few more areas
* Test more invalid MBID's

## 0.3.1

### Fixed

* Fixed typing for `array|object` returns

## 0.3.0

First fork release updating the code to require PHP8.2 minimum.

No functional changes but many code quality updates and strict typing is enabled.

### Added

* CHANGELOG.md
* Suggest ext-curl in composer.json
* Dev tools added to composer.json
  * php-cs-fixer
  * phpstan
  * rector
* Composer commands to run dev tools
* Function and parameter typing

### Changed

* Redirect repository information from [mikealmond](https://github.com/mikealmond/musicbrainz) to [lachlan-00](https://github.com/lachlan-00/musicbrainz)
* MusicBrainz::isValidMBID returns bool only
* Added requests and guzzle to dev requirements instead of suggestions
* Strict typing enforced `declare(strict_types=1);`
* Update phpunit and fixed up tests for newer version
* Set initial userAgent property from a VERSION constant

### Removed

* PHP support less than PHP8.2
* GitHub funding link

### Fixed

* Missing countries in the countries data array
* Dynamic property declarations
* Old Requests import method causing deprecation warnings
* [fix search, add missing entities](https://github.com/lachlan-00/MusicBrainz/pull/1)
