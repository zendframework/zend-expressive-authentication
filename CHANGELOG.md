# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## 0.4.0 - 2018-03-15

### Added

- [#15](https://github.com/zendframework/zend-expressive-authentication/pull/15)
  adds support for PSR-15.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- [#15](https://github.com/zendframework/zend-expressive-authentication/pull/15) and
  [#3](https://github.com/zendframework/zend-expressive-authentication/pull/3)
  remove support for http-interop/http-middleware and
  http-interop/http-server-middleware.

- [#19](https://github.com/zendframework/zend-expressive-authentication/pull/19)
  removes `Zend\Expressive\Authentication\ResponsePrototypeTrait`; the approach
  was flawed, and the various adapters will be updated to compose response
  factories instead of instances.

### Fixed

- [#18](https://github.com/zendframework/zend-expressive-authentication/pull/18)
  uses the `ResponseInterface` as a factory. This was recently changed in
  [zend-expressive#561](https://github.com/zendframework/zend-expressive/pull/561).

## 0.3.1 - 2018-03-12

### Added

- Nothing.

### Changed

- [#22](https://github.com/zendframework/zend-expressive-authentication/issues/22)
  updates the `ResponsePrototypeTrait` to allow callable `ResponseInterface`
  services (instead of those directly returning a `ResponseInterface`).

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 0.3.0 - 2018-01-24

### Added

- Nothing.

### Changed

- [#14](https://github.com/zendframework/zend-expressive-authentication/issues/14)
  renames the method `UserInterface::getUsername()` to
  `UserInterface::getIdentity()`.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [#13](https://github.com/zendframework/zend-expressive-authentication/pull/13)
  fixes an issue whereby fetching a record by an unknown username resulted in a
  "Trying to get property of non-object" error when using the `PdoDatabase` user
  repository implementation.

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
