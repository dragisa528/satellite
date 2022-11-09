<?php
/**
 * Plugin Name:     Satellite
 * Plugin URI:      https://github.com/eighteen73/satellite
 * Description:     A collection of developer tools for WordPress projects
 * Author:          Orphans Web Team
 * Author URI:      https://eighteen73.co.u
 * Update URI:      https://github.com/eighteen73/satellite
 * Text Domain:     satellite
 * Domain Path:     /languages
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
