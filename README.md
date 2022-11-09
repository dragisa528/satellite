# Satellite

Satellite applies a collection of developer tools for WordPress projects. It is only designed to be used in development environments and should not be installed in live environments at all.

The plugin is unapologetically opinionated to fit the needs and preferences of our web agency. We feel the choices we've made are sensible and pragmatic for the kinds of websites we work on. We understand not everyone will agree with the choices we have made.

## Installation

These instructions assume you are using [Nebula](https://github.com/eighteen73/nebula). That is a not a requirement (e.g. it has also been tested in Bedrock) but the configuration may need to be altered if you are not using it.

To install the plugin run the following command:

```shell
composer require --dev eighteen73/satellite
```

Satellite will be installed as a must-use plugin, so it is automatically enabled.

## Configuration

The plugin loads configuration from environment settings. There is no UI and settings are not stored in the website's database. 

You may store config in your website's environment settings file (e.g. `config/environments/development.php`) if you want to share it with orther developers, or in your `.env` file to keep it private. If config is stored in both places your `.env` file will take precedence.

This file's examples show configuration in `.env` format but it can be adjusted as follows of putting it into a evironment settings file. I.e. `SATELLITE_PRODUCTION_URL=example.com` would be changed to `Config::define( 'SATELLITE_PRODUCTION_URL', 'website.example.com' )`.

Feature specific configuration is mentioned in their descriptions below. 

## Features

- [Feature: Use remote files](#remote-files)
- [Feature: Mail catcher](#mail-catcher)

___

<a name="remote-files"></a>
### Feature: Use remote files

#### Purpose

This plugin will remote-load uploaded files without having to sync them to your development environment. Note this only works when `wp_get_environment_type()` = `local|development` and will silently deactivate when that is not the case, or when the following config is not in place.

#### Config

```ini
SATELLITE_PRODUCTION_URL=https://example.com
```

#### Usage

There are no special instructions for use.

___

<a name="mail-catcher"></a>
### Feature: Mail catcher

#### Purpose

As a safety precaution to save developers from accidentally sending emails to real users, this plugin tries to override mail settings so all emails will be directed to a mail catcher instead.

You should always veryify that functionality works as expected in case you are using another email plugin that is especially aggressive at applying it's own settings.

#### Config

```ini
SATELLITE_SMTP_HOST=127.0.0.1
SATELLITE_SMTP_PORT=1025
SATELLITE_SMTP_ENCRYPTION=[none|tls|ssl]
SATELLITE_SMTP_AUTH=[true|false]
SATELLITE_SMTP_USERNAME=
SATELLITE_SMTP_PASSWORD=
```

#### Usage

There are no special instructions for use.

Note that is plugin automatically disables some commonly used mail plugins, notably:

- wp-mail-smtp
- easy-wp-smtp
- mailgun
