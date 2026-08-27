# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [v0.6.0] - 2026-08-26

### Added
- `debug` command to start the internal PHP server with Xdebug enabled.
- `--dump-router` (`-d`) option on the `debug` command to output the router command string.
- Testing infrastructure and mocking utilities for models and database interactions (`ModelMock`, `TestContext`, `TestingModelFactory`, stub database drivers and adapters).
- Error logging for caught exceptions in the development router.

## [v0.5.0] - 2025-04-05

### Changed
- Made the asset builder a lot more consistent in how inputs and outputs are expressed.

### Removed
- Removed the initialize command so the only way to initialize a new app will be through the web interface.

### Fixed
- Fixed several bugs in caching and exception reporting.

## [v0.4.0] - 2024-07-12

### Changed
- Cleaned up the dev tool before yet another ntentan reconstruction.

## [v0.3.0] - 2021-11-29

### Added
- An `init` command to help with the creation of new projects.
- A builtin template program to get users started.

## [v0.2.3] - 2019-01-10

### Added
- First release with a changelog.

[Unreleased]: https://github.com/ntentan/dev/compare/v0.5.0...HEAD
[v0.5.0]: https://github.com/ntentan/dev/compare/v0.4.0...v0.5.0
[v0.4.0]: https://github.com/ntentan/dev/compare/v0.3.0...v0.4.0
[v0.3.0]: https://github.com/ntentan/dev/compare/v0.2.3...v0.3.0
[v0.2.3]: https://github.com/ntentan/dev/releases/tag/v0.2.3
