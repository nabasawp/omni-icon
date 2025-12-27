# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Fixed
- Copy `.discovery-skip` files manually after php-scoper runs, as php-scoper only processes PHP files by default.

## [1.0.4] - 2025-12-27

## [1.0.3] - 2025-12-27

### Fixed
- Configure php-scoper to properly include `.discovery-skip` files in the release package (the fix in 1.0.2 was incomplete).

## [1.0.2] - 2025-12-27

### Fixed
- The `.discovery-skip` files are now included in the release package to ensure the Discovery feature works correctly.

## [1.0.1] - 2025-12-27

### Fixed
- The Discovery feature is not functioning correctly because the release package is missing the `composer.json` file.

## [1.0.0] - 2025-12-27

### Added
- 🐣 Initial release.

[unreleased]: https://github.com/nabasa-dev/omni-icon/compare/1.0.4...HEAD
[1.0.4]: https://github.com/nabasa-dev/omni-icon/compare/1.0.3...1.0.4
[1.0.3]: https://github.com/nabasa-dev/omni-icon/compare/1.0.2...1.0.3
[1.0.2]: https://github.com/nabasa-dev/omni-icon/compare/1.0.1...1.0.2
[1.0.1]: https://github.com/nabasa-dev/omni-icon/compare/1.0.0...1.0.1
[1.0.0]: https://github.com/nabasa-dev/omni-icon/compare/main...1.0.0