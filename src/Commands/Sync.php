<?php

namespace Orphans\Satellite\Commands;

use Orphans\Satellite\Traits\EnvReader;
use Roots\WPConfig\Config;
use Roots\WPConfig\Exceptions\UndefinedConfigKeyException;
use WP_CLI;

class Sync
{
    use EnvReader;

    private array $options = [
        'database' => false,
        'uploads' => false,
        'active_plugins' => true,
        'inactive_plugins' => true,
    ];

    private array $settings = [
        'ssh_host' => null,
        'ssh_port' => '22',
        'ssh_user' => null,
        'ssh_path' => null,
    ];

    public function run($args, $assoc_args)
    {
        if (!$this->isSafeEnvironment()) {
            WP_CLI::error('This can only be run in a development and staging environments. Check your WP_ENV setting.');
        }

        if (!$this->hasAllSettings()) {
            WP_CLI::error('You are missing some config settings in your environment. Please refer to the plugin\'s README.md.');
        }

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
     * Development and staging use only
     */
    private function isSafeEnvironment(): bool
    {
        return WP_ENV === 'development' || WP_ENV === 'staging';
    }

    /**
     * Load settings from .env or config (.env takes precedence)
     */
    private function hasAllSettings(): bool
    {
        try {
            $this->settings['ssh_host'] = $this->env('SATELLITE_SSH_HOST') ?: Config::get('SATELLITE_SSH_HOST');
            $this->settings['ssh_user'] = $this->env('SATELLITE_SSH_USER') ?: Config::get('SATELLITE_SSH_USER');
            $this->settings['ssh_path'] = $this->env('SATELLITE_SSH_PATH') ?: Config::get('SATELLITE_SSH_PATH');
        } catch (UndefinedConfigKeyException $e) {
            return false;
        }

        // Special case for SSH port
        try {
            $ssh_port = $this->env('SATELLITE_SSH_PORT') ?: Config::get('SATELLITE_SSH_PORT');
            $this->settings['ssh_port'] = strval($ssh_port);
            if (!preg_match('/^[0-9]+$/', $this->settings['ssh_port'])) {
                $this->settings['ssh_port'] = null;
            }
        } catch (UndefinedConfigKeyException $e) {
            // Do nothing
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

    private function printActionTitle(string $title)
    {
        WP_CLI::line(WP_CLI::colorize('%b'));
        WP_CLI::line(strtoupper($title));
        WP_CLI::line(WP_CLI::colorize(str_pad('', strlen($title), '~') . '%n'));
    }

    private function fetchDatabase()
    {
        $this->printActionTitle('Fetching database');
        WP_CLI::line(WP_CLI::colorize('%y// todo%n'));
    }

    private function fetchUploads()
    {
        $this->printActionTitle('Fetching uploads');
        WP_CLI::line(WP_CLI::colorize('%y// todo%n'));
    }

    private function activatePlugins()
    {
        $this->printActionTitle('Activating Plugins');
        WP_CLI::line(WP_CLI::colorize('%y// todo%n'));
    }

    private function deactivatePlugins()
    {
        $this->printActionTitle('Deactivating Plugins');
        WP_CLI::line(WP_CLI::colorize('%y// todo%n'));
    }
}
