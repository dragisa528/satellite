<?php

namespace Orphans\Satellite;

use Roots\WPConfig\Config;
use Roots\WPConfig\Exceptions\UndefinedConfigKeyException;
use WP_CLI;
use function Env\env;

class Sync
{
    private $options = [
        'database' => false,
        'uploads' => false,
        'active_plugins' => true,
        'inactive_plugins' => true,
    ];

    private $settings = [
        'ssh_host' => null,
        'ssh_user' => null,
        'ssh_path' => null,
    ];

    /**
     * Prepares development environment and optionally fetches remote database & uploaded files.
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
     *     wp sync --database --files
     *
     * @when after_wp_load
     */
    public function run($args, $assoc_args)
    {
        if (!$this->isSafeEnvironment()) {
            WP_CLI::error('This can only be run in a development environment. Check your WP_ENV setting.');
        }

        if (!$this->hasAllSettings()) {
            WP_CLI::error('You are missing some config settings in your environment. Please refer to the plugin\'s README.md.');
        }

        var_dump($this->settings);

        $this->getOptions($assoc_args);

        if ($this->options['database']) {
            $this->fetchDatabase();
        }

        if ($this->options['uploads']) {
            $this->fetchUploads();
        }

        if ($this->options['active_plugins']) {
            $this->activatePlugins();
        }

        if ($this->options['inactive_plugins']) {
            $this->deactivatePlugins();
        }

        WP_CLI::line();
        WP_CLI::success('All done!');
    }

    /**
     * Development use only
     */
    private function isSafeEnvironment(): bool
    {
        return WP_ENV === 'development';
    }

    /**
     * Load settings from .env or config (.env takes precedence)
     */
    private function hasAllSettings(): bool
    {
        try {
            $this->settings['ssh_host'] = env('SATELLITE_SSH_HOST') ?: Config::get('SATELLITE_SSH_HOST');
            $this->settings['ssh_user'] = env('SATELLITE_SSH_USER') ?: Config::get('SATELLITE_SSH_USER');
            $this->settings['ssh_path'] = env('SATELLITE_SSH_PATH') ?: Config::get('SATELLITE_SSH_PATH');
        } catch (UndefinedConfigKeyException $e) {
            return false;
        }
        foreach ($this->settings as $setting) {
            if (empty($setting)) {
                return false;
            }
        }
        return true;
    }

    private function getOptions($assoc_args)
    {
        $true_values = [true, 'true', 1, '1', 'yes'];
        if (isset($assoc_args['database'])) {
            $this->options['database'] = in_array($assoc_args['database'], $true_values, true);
        }
        if (isset($assoc_args['uploads'])) {
            $this->options['uploads'] = in_array($assoc_args['uploads'], $true_values, true);
        }
    }

    private function fetchDatabase()
    {
        WP_CLI::line();
        WP_CLI::line('Fetching database');
        WP_CLI::line('=================');
    }

    private function fetchUploads()
    {
        WP_CLI::line();
        WP_CLI::line('Fetching uploads');
        WP_CLI::line('================');
    }

    private function activatePlugins()
    {
        WP_CLI::line();
        WP_CLI::line('Activating Plugins');
        WP_CLI::line('==================');
    }

    private function deactivatePlugins()
    {
        WP_CLI::line();
        WP_CLI::line('Deactivating Plugins');
        WP_CLI::line('====================');
    }
}
