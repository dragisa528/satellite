<?php

namespace Eighteen73\Satellite;

use Eighteen73\Satellite\Sync\Sync;
use WP_CLI;
use WP_CLI_Command;

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
	 */
	public function sync( array $args = [], array $assoc_args = [] ) {
		$sync = new Sync();
		$sync->run( $args, $assoc_args );
	}
}
