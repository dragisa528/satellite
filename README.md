These instructions assume you are using Roots' Bedrock but that is not a prerequisite of the module so
adapt the following as necessary.

## RemoteFiles

This plugin will remote-load uploaded files without having to sync them to your development environment. Note this only works when `WP_ENV=development` and will silently deactivate when that is not the case, or when the following config is not in place. 

You must have the following set in `config/environments/development.php` or `.env`. If both are set, `.env` will take precedence. This allows you to override project defaults without affecting other developers.

```php
Config::define('SATELLITE_PRODUCTION_URL', 'https://example.com');
```

or

```dotenv
SATELLITE_PRODUCTION_URL=https://example.com
```

## WP Command

The command `wp satellite sync` can be used to update your local environment using remote content. This can only be run when `WP_ENV=development|staging`.

You must have the following set in `config/environments/(development|staging).php`, or `.env`. If both are set, `.env` will take precedence. This allows you to override project defaults without affecting other developers.

```php
Config::define('SATELLITE_SSH_HOST', 'website.example.com');
Config::define('SATELLITE_SSH_HOST', 123); // if not port 22
Config::define('SATELLITE_SSH_USER', 'username');
Config::define('SATELLITE_SSH_PATH', '/path/to/remote/website');
```

or

```dotenv
SATELLITE_SSH_HOST=website.example.com
SATELLITE_SSH_HOST=123 # if not port 22
SATELLITE_SSH_USER=username
SATELLITE_SSH_PATH=/path/to/remote/website
```

Command examples:

```bash
# Simple, just applies plugin overrides 
wp satellite sync

# Database mode, downloads a fresh copy of the remove database (overwriting all local data) and applies plugin overrides 
wp satellite sync --database

# Uploads mode, downloads a fresh copy of uploads directory (overwriting all local files) and applies plugin overrides 
wp satellite sync --uploads

# All of the above 
wp satellite sync --database --uploads
```

Note that `--uploads` is usually unnecessary if remote files are enabled as specified above.
