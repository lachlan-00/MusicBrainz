# MusicBrainz

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

* Moved GuzzleHttp 3.8 to GuzzleHttpOld class
* Moved MusicBrainz Entity objects to the Entities folder
* Moved MusicBrainz Data objects to the Objects folder
* Move some of the larger data arrays to their Entity classes
* Require `Entity::getName()` for Entity objects. (`getTitle()` is still available where the object has a title property)

### Fixed

* Updated examples to use new GuzzleHttp version
* Missing Entity links for filtering by object

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

* PHP support less that PHP8.2
* GitHub funding link

### Fixed

* Missing countries in the countries data array
* Dynamic property declarations
* Old Requests import method causing deprecation warnings
* [fix search, add missing entities](https://github.com/lachlan-00/MusicBrainz/pull/1)
