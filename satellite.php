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

use Eighteen73\Satellite\MailCatcher\MailCatcher;
use Eighteen73\Satellite\RemoteFiles\RemoteFiles;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

spl_autoload_register(
	function ( $class_name ) {
		$path_parts = explode( '\\', $class_name );

		if ( ! empty( $path_parts ) ) {
			$package = $path_parts[0];

			unset( $path_parts[0] );

			if ( 'Orbit' === $package ) {
				require_once __DIR__ . '/includes/classes/' . implode( '/', $path_parts ) . '.php';
			}
		}
	}
);

RemoteFiles::instance()->setup();
MailCatcher::instance()->setup();

// Tell wp-cli about our `satellite` command
WP_CLI::add_command( 'satellite', 'Eighteen73\Satellite\SatelliteCLI' );
