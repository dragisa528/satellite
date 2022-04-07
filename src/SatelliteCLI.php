<?php

namespace Orphans\Satellite;

use Orphans\Satellite\Commands\Sync;
use WP_CLI;
use WP_CLI_Command;

class SatelliteCLI extends WP_CLI_Command
{
    public function __construct()
    {
        if (!is_plugin_active('satellite/satellite.php')) {
            WP_CLI::error('Satellite is not enabled.');
        }
    }

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
    public function sync(array $args = [], array $assoc_args = [])
    {
        $sync = new Sync();
        $sync->run($args, $assoc_args);
    }
}
