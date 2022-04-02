These instructions assume you are using Roots' Bedrock but that is not a prerequisite of the module so
adapt the following as necessary.

## RemoteFiles

Add the website's remote URL to `config/environments/development.php` to load user-uploaded files without 
syncing them to your development environemnt.

```php
Config::define('SATELLITE_PRODUCTION_URL', 'https://example.com');
```
