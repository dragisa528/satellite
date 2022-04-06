<?php
/**
 * Plugin Name:     Satellite
 * Plugin URI:      PLUGIN SITE HERE
 * Description:     A collection of developer tools for WordPress projects
 * Author:          Orphans Web Team
 * Author URI:      https://orphans.co.uk
 * Update URI:      https://code.orphans.co.uk/packages/wordpress/satellite/
 * Text Domain:     satellite
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Satellite
 */

use Orphans\Satellite\RemoteFiles;
use Orphans\Satellite\Sync;

// Init the class that loads remote files in place of storing them locally
new RemoteFiles;

// Tell wp-cli about our `sync` command
$sync = new Sync();
WP_CLI::add_command('sync', [$sync, 'run']);
