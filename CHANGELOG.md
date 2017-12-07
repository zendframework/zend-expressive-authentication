# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## 1.0.0alpha1 - TBD

### Added

- [#3](https://github.com/zendframework/zend-expressive-authentication/pull/3)
  adds support for http-interop/http-middleware.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- [#3](https://github.com/zendframework/zend-expressive-authentication/pull/3)
  removes support for http-interop/http-middleware.

### Fixed

- Nothing.

## 0.2.0 - 2017-11-27

### Added

- Nothing.

### Changed

- [#4](https://github.com/zendframework/zend-expressive-authentication/pull/4)
  renames the method `UserInterface::getUserRole()` to
  `UserInterface::getUserRoles()`. The method MUST return an array of string
  role names.

- [#4](https://github.com/zendframework/zend-expressive-authentication/pull/4)
  renames the method `UserRepositoryInterface::getRoleFromUser()` to
  `UserRepositoryInterface::getRolesFromUser()`. The method MUST return an array
  of string role names.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 0.1.0 - 2017-11-08

Initial release.

### Added

- Everything.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.
