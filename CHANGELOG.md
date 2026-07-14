# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## Unreleased

### Added

- Add `blocks()->meetingNotes()->query()` (Notion API `2026-03-11`) to search AI meeting notes across the workspace by title, attendees, dates, and editors, with sorting and a result limit — the endpoint has no cursor pagination.
- Add comment editing (Notion API `2026-03-11`): `comments()->update()` and `updateFromMarkdown()` rewrite a comment's content, `delete()` removes it, and `createFromMarkdown()` writes a new comment as inline markdown. A connection can only edit comments it created.
- Expose `block_type` on `UnsupportedBlock`, identifying the underlying block type (for example `form` or `button`) the API cannot represent.

### Fixed

- Add support for `agent_id` page parents so pages parented to a Notion agent hydrate instead of throwing `UnsupportedParentTypeException`.

## 1.10.0 - 2026-07-14

### Added

- Add file-upload embeds: `EmbedProperty::fromFileUpload()` attaches an uploaded file to an embed block, which is how the Notion API renders uploaded `.html` files as HTML blocks. Responses hydrate the temporary signed `url` the API returns.
- Add per-request headers and raw (non-JSON) request bodies to `RequestParameters`, unblocking `multipart/form-data` endpoints such as the Notion File Upload API.
- Add `php-http/multipart-stream-builder` as a dependency for building multipart request bodies.
- Add support for the Notion File Upload API via `$notion->fileUploads()`: a one-call `upload()` for the common case, plus create, send/sendPart (`multipart/form-data`), complete, retrieve, and list.
- Add the `file_upload` file-object type so blocks referencing uploaded files hydrate instead of throwing `UnsupportedFileTypeException`.
- Add support for Notion API version `2026-03-11`, opt-in per client via `setNotionVersion(ClientOptions::NOTION_VERSION_2026_03_11)`: write payloads use `in_trash` instead of `archived`, and the new `blocks()->children()->append()` insertion position serializes as `position` instead of `after`. Clients on older versions send byte-identical payloads to before, and two clients on different versions can run side by side in one process.
- Add `NOTION_VERSION_2022_06_28`, `NOTION_VERSION_2025_09_03`, and `NOTION_VERSION_2026_03_11` constants on `ClientOptions`; the default version is unchanged.
- Add `isInTrash()`/`setInTrash()` on `Page`, blocks, and `Database`, and accept both the `archived` and `in_trash` response keys during hydration — including for data sources — regardless of the client version.
- Add an optional `$afterBlockId` parameter to `blocks()->children()->append()` to insert blocks after an existing block.
- Add `tab`, `meeting_notes`, and `transcription` block classes so these blocks hydrate with their content instead of degrading to `UnsupportedBlock` and dropping the payload. Meeting notes and transcription blocks are read-only at the API; tab blocks are creatable, with the label carried by paragraph children.
- Add markdown page content support (Notion API `2026-03-11`): `pages()->createFromMarkdown()`, `retrieveMarkdown()`, and `updateMarkdown()` with a `PageMarkdownRequest` builder covering the `update_content`, `replace_content`, `insert_content`, and `replace_content_range` commands.
- Add async-task support for long markdown operations: `createFromMarkdownAsync()`/`updateMarkdownAsync()` return an `AsyncTask` to poll via the new `$notion->asyncTasks()->retrieve()`.
- Add `FILTER_VALUE_PAGE`, `FILTER_VALUE_DATA_SOURCE`, and legacy `FILTER_VALUE_DATABASE` constants on `SearchRequest` for the search filter values.

### Changed

- Default request headers (`Notion-Version`, `Content-Type`, `User-Agent`) are now applied only when a request does not already set them, so a per-request `Content-Type` takes precedence over the JSON default. Requests that set no headers are unchanged.

### Fixed

- `databases()->update()` on Notion API `2025-09-03` or newer no longer sends `properties` (schema changes moved to `dataSources()->update()`) and omits unset `icon`/`cover`, which that version rejects as explicit nulls. Payloads on older versions are unchanged.
- Blocks sent as children in `blocks()->children()->append()` and `pages()->create()` now serialize only creatable fields (`object`, `type`, and the block's own property) via `AbstractBlock::toArrayForCreate()`. The Notion API rejects payloads carrying read-only fields such as `archived` with a validation error.
- Nested blocks set with `setChildren()` now serialize inside the block's type property — the only shape the Notion API accepts — and recursively emit only creatable fields, so appending a block with nested children works instead of failing validation.

## 1.9.0 - 2026-05-28

### Added

- Add support for Notion `heading_4`, `heading_5`, and `heading_6` blocks through `Heading4Block`, `Heading5Block`, and `Heading6Block`.

## 1.8.3 - 2026-05-10

### Fixed

- Add support for documented Notion `block_id` page parents so page and search hydration no longer throw `UnsupportedParentTypeException` for block-parent pages.

## 1.8.2 - 2026-03-27

### Fixed

- Fix page serialization for `archived` (issue #67): omit `archived` when unset, and preserve explicit `false`/`true` values for create/update payloads.
- Add endpoint and resource regression coverage for create/update payload serialization, including retrieve -> update flow.

## 1.8.1 - 2026-03-25

### Fixed

- Add support for Notion native icon payloads (`type: "icon"`) to prevent `UnsupportedFileTypeException` during resource hydration.
- Add regression and round-trip serialization tests for icon hydration paths used by `databases()->query()`.

## 1.8.0 - 2026-02-23

### Added

- Add support for data sources (Notion API 2025-09-03), including data source endpoints and `data_source_id` support.

### Changed

- Default Notion API version is now `2022-06-28`.
- Creating databases on Notion API 2025-09-03+ now uses `initial_data_source` for properties.

### Fixed

- Normalize empty database property configurations so empty objects serialize as `{}` where required by the API.

## 1.7.3 - 2026-02-19

### Fixed

- Avoid PHP warnings when Notion returns a `synced_from` object without a `block_id` key.

## 1.7.2 - 2025-10-23

### Fixed

- Fixed "Undefined array key 'block_id'" error in `SyncedFromProperty`. The SDK now properly handles synced blocks that don't include a `block_id` field in the API response.

## 1.7.1 - 2025-10-08

### Fixed

- Fixed "Undefined array key 'owner'" error when creating pages in databases. The SDK now properly handles empty bot objects returned by the Notion API in `created_by` and `last_edited_by` fields.

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
