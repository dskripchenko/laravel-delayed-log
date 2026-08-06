# Changelog

All notable changes to this project are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project follows [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

Entries for releases published before this file existed were reconstructed from
the tagged commit history.

## [1.1.1] - 2026-07-21

### Added
- GitHub Actions pipeline: PHP 8.2-8.5 against Laravel 11, 12 and 13.
- A README that explains what the package does, how to install it and how to use it.

### Removed
- `roave/security-advisories` from the development dependencies: it refuses to install
  alongside Laravel 11, which is still supported here.

## [1.1.0] - 2026-07-20

### Changed
- Supported Laravel versions are 11, 12 and 13. Laravel 10 reached end of life and was dropped.

## [1.0.7] - 2025-03-12

### Added
- Laravel 12 support.

## [1.0.6] - 2024-07-09

### Added
- Laravel 11 support.

## [1.0.5] - 2023-12-12

### Added
- The queue name used by the logging job can be configured.

## [1.0.4] - 2023-12-12

### Fixed
- Corrected the handler passed to the delayed log component.

## [1.0.3] - 2023-12-12

### Changed
- The formatter is resolved by type rather than by name.

## [1.0.2] - 2023-12-12

### Added
- Custom serializers for log records.

## [1.0.1] - 2023-12-12

### Fixed
- The logger channel is registered under the configured name.

## [1.0.0] - 2023-12-12

### Added
- First release: writing log records through a queued job instead of the request cycle.
