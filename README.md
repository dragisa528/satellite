These instructions assume you are using Roots' Bedrock but that is not a prerequisite of the module so
adapt the following as necessary.

## RemoteFiles

Add the website's remote URL to `config/environments/development.php` to load user-uploaded files without 
syncing them to your development environemnt.

```php
Config::define('SATELLITE_PRODUCTION_URL', 'https://example.com');
```

## Sync

The command `wp sync` can be used to update your local environment using remote content. This can only be run when WP_ENV == "development".

```bash
# Simple, just applies plugin overrides 
wp sync

# Database mode, downloads a fresh copy of the remove database (overwriting all local data) and applies plugin overrides 
wp sync --database

# Uploads mode, downloads a fresh copy of uploads directory (overwriting all local files) and applies plugin overrides 
wp sync --uploads

# All of the above 
wp sync --database --uploads
```

Note that `--uploads` is usually necessary if remote files are enabled as specified above.
