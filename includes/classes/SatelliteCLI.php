<?php
/**
 * WP-CLI commands for this plugin
 *
 * @package         Satellite
 */

namespace Eighteen73\Satellite;

use Eighteen73\Satellite\Sync\Sync;
use WP_CLI_Command;

/**
 * WP-CLI commands for this plugin
 */
class SatelliteCLI extends WP_CLI_Command {
	/**
	 * Prepares development or staging environment and optionally fetches remote database & uploaded files.
	 *
	 * ## OPTIONS
	 *
	 * [--database]
	 * : Fetch the remote database
	 *
	 * [--uploads]
	 * : Fetch the remote uploaded files
	 *
	 * ## EXAMPLES
	 *
	 *     wp satellite sync --database --files
	 *
	 * @subcommand sync
	 * @when after_wp_load
	 *
	 * @param array $args User arguments
	 * @param array $assoc_args User arguments
	 *
	 * @return void
	 */
	public function sync( array $args = [], array $assoc_args = [] ) {
		$sync = new Sync();
		$sync->run( $args, $assoc_args );
	}
}
