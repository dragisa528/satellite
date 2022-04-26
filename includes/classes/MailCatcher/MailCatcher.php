<?php

namespace Satellite\MailCatcher;

use Satellite\EnvReader;
use Satellite\Singleton;

/**
 * Local Mail Catcher class.
 * Disables all emails from being sent and instead
 * routes to a local mailcatcher instance if available.
 */
class MailCatcher extends Singleton {
	use EnvReader;

	private $host;
	private $port;
	private $encryption;
	private $auth;
	private $username;
	private $password;

	/**
	 * Plugins that should be disabled.
	 */
	private $plugins = [
		'wp-mail-smtp/wp_mail_smtp.php',
		'easy-wp-smtp/easy-wp-smtp.php',
	];

	/**
	 * Constructor.
	 */
	public function setup() {
		$this->host       = $this->env( 'SATELLITE_SMTP_HOST' ) ?? '127.0.0.1';
		$this->port       = $this->env( 'SATELLITE_SMTP_PORT' ) ?? 1025;
		$this->encryption = $this->env( 'SATELLITE_SMTP_ENCRYPTION' ) ?? false;
		$this->auth       = $this->env( 'SATELLITE_SMTP_AUTH' ) ?? false;
		$this->username   = $this->env( 'SATELLITE_SMTP_USERNAME' ) ?? false;
		$this->Password   = $this->env( 'SATELLITE_SMTP_PASSWORD' ) ?? false;

		add_action( 'phpmailer_init', [ $this, 'phpmailerInit' ], 999 );
		add_action( 'admin_init', [ $this, 'disablePlugins' ], 999 );
	}

	/**
	 * Check if we're on a non-production environment.
	 *
	 * @return bool
	 */
	private function isSafeEnvironment(): bool {
		return WP_ENV === 'development';
	}

	/**
	 * Sets wp_mail() to use PHPMailer as the mailer with SMTP settings.
	 *
	 * @param PHPMailer $phpmailer
	 *
	 * @return void
	 */
	public function phpmailerInit( \PHPMailer\PHPMailer\PHPMailer $phpmailer ) {

		$phpmailer->isSMTP();
		$phpmailer->Host       = $this->host;
		$phpmailer->Port       = $this->port;
		$phpmailer->SMTPSecure = $this->encryption;
		$phpmailer->SMTPAuth   = $this->auth;
		$phpmailer->Username   = $this->username;
		$phpmailer->Password   = $this->password;
	}

	/**
	 * Checks if any of the plugins in the $plugins array are active.
	 * If so, disable them for selected environments environments.
	 *
	 * @return void
	 */
	public function disablePlugins() {

		if ( ! $this->isSafeEnvironment() ) {
			return;
		}

		foreach ( $this->plugins as $plugin ) {
			if ( is_plugin_active( $plugin ) ) {
				deactivate_plugins( $plugin, true );
			}
		}
	}
}
