# Sign in With SS14

![License](https://img.shields.io/badge/license-MIT-blue.svg) [![Latest Stable Version](https://img.shields.io/packagist/v/valtos/oidc-ss14.svg)](https://packagist.org/packages/valtos/oidc-ss14) [![Total Downloads](https://img.shields.io/packagist/dt/valtos/oidc-ss14.svg)](https://packagist.org/packages/valtos/oidc-ss14)

A [Flarum](http://flarum.org) extension. Sign in with SS14

## Installation

Install with composer:

```sh
composer require valtos/oidc-ss14:"*"
```

## Updating

```sh
composer update valtos/oidc-ss14
php flarum cache:clear
```

## Configuration

Once enabled, this extension will add a `SS14` option to the settings page of `fof/oauth`. Toggle `SS14` on, and hit the configure icon.

It is **imperitive** that you grant the following scopes to your new application at SS14:
- `openid`
- `email`
- `profile`

Set the callback URL as given in the extension settings.

Enter the `Client ID` and `Client Secret` as displayed in the `Basic Information` page at SS14 into the Flarum configuration.

Enjoy logging in with your SS14 credentials!

## Links

- [Packagist](https://packagist.org/packages/valtos/oidc-ss14)
