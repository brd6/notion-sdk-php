# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## 1.7.0 - 2025-06-25

### Added

- Add support for `select` and `multi_select` values in rollup properties (fixes #41).

### Changed

- Blocks with children are now handled by the `AbstractBlock` class instead of specific block classes.

### Fixed

- Fixed a deprecation warning in the test suite.
- Fixed database queries failing when rollup fields contain select or multi-select values.

## 1.6.0 - 2025-04-14

### Added

- Add support for unique id and verification properties.

## 1.5.0 - 2024-12-10

### Added

- Add support for custom emoji in mentions.

## 1.4.0 - 2024-12-09

### Added

- Add support for custom emoji.

## 1.3.3 - 2024-23-10

### Fixed

- Check if `icon` exists in Callout block before accessing it.

## 1.3.2 - 2024-05-10

### Fixed

- Added support for multiple rich text objects in table row cells.

## 1.3.1 - 2024-04-10

### Fixed

- Allow empty cell in table block

## 1.3.0 - 2024-04-10

### Added

- Add support for audio block

### Added

- Add support for link mentions in text property

## 1.2.3 - 2024-03-09

### Added

- Add caption to file property

## 1.2.2 - 2023-10-29

### Fixed

- Fixed type coercion issue to properly handle decimal values.

## 1.2.1 - 2023-10-24

### Added

- Add missing Status property value type

## 1.2.0 - 2023-10-24

### Added

- Add support for Status property in database

## 1.1.8 - 2023-03-09

### Fixed

- Allow empty string in serialized data

## 1.1.7 - 2022-12-09

### Fixed

- Fix table row cells

## 1.1.6 - 2022-09-14

### Fixed

- Fix Synced Block property name

## 1.1.5 - 2022-09-08

### Fixed

- Fix wrong access to Number

## 1.1.4 - 2022-07-23

### Fixed

- Fix files property in page

## 1.1.3 - 2022-07-21

### Fixed

- Set default content type in header
- Fix search query with pagination and filter

## 1.1.2 - 2022-07-15

### Fixed

- Email field in person property can be null

## 1.1.1 - 2022-06-25

### Fixed

- Database query with pagination

## 1.1.0 - 2022-06-16

### Changed

- Use HTTPlug as http client abstraction

## 1.0.0 - 2022-04-16

Initial release.

### Added

- Full support of Notion API (2022-02-22)
