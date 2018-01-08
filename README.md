# Expressive Authentication Middleware

[![Build Status](https://secure.travis-ci.org/zendframework/zend-expressive-authentication.svg?branch=master)](https://secure.travis-ci.org/zendframework/zend-expressive-authentication)
[![Coverage Status](https://coveralls.io/repos/github/zendframework/zend-expressive-authentication/badge.svg?branch=master)](https://coveralls.io/github/zendframework/zend-expressive-authentication?branch=master)

zend-expressive-authentication provides middleware for [Expressive](https://docs.zendframework.com/zend-expressive/)
and [PSR-7](http://www.php-fig.org/psr/psr-7/) applications for the purpose of
authenticating HTTP requests using consumer- or third-party-provided adapters.

## WORK IN PROGRESS

This repository contains a **work in progress** project for building an
authentication module for *Expressive* and *PSR-7* applications.

**Please, do not use this code in a production environment!**

## Installation

You can install the *zend-expressive-authentication* library with composer:

```bash
$ composer require zendframework/zend-expressive-authentication
```

## Documentation

Documentation is [in the doc tree](doc/book/), and can be compiled using [mkdocs](http://www.mkdocs.org):

```bash
$ mkdocs build
```

You may also [browse the documentation online](https://docs.zendframework.com/zend-expressive-authentication/).
