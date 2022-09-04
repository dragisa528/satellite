<?php
/**
 * Use a local mailcatcher so messages never leave this environment
 *
 * @package         Satellite
 */

namespace Eighteen73\Satellite\MailCatcher;

use Eighteen73\Satellite\Environment;
use Eighteen73\Satellite\Singleton;
use PHPMailer\PHPMailer\PHPMailer;

/**
 * Local Mail Catcher class.
 * Disables all emails from being sent and instead
 * routes to a local mailcatcher instance if available.
 */
class MailCatcher extends Singleton {

	use Environment;

	/**
	 * SMTP hostname or IP
	 *
	 * @var string
	 */
	private $host;

	/**
	 * SMTP port
	 *
	 * @var int
	 */
	private $port;

	/**
	 * SMTP encryption type
	 *
	 * @var string
	 */
	private $encryption;

	/**
	 * Is auth enabled
	 *
	 * @var bool
	 */
	private $auth;

	/**
	 * SMTP username
	 *
	 * @var string
	 */
	private $username;

	/**
	 * SMTP password
	 *
	 * @var string
	 */
	private $password;

	/**
	 * Plugins that should be disabled.
	 *
	 * @var array
	 */
	private $plugins = [
		'wp-mail-smtp/wp_mail_smtp.php',
		'easy-wp-smtp/easy-wp-smtp.php',
		'mailgun/mailgun.php',
	];

	/**
	 * Constructor.
	 */
	public function setup() {
		$this->host       = getenv( 'SATELLITE_SMTP_HOST' ) ?: '127.0.0.1';
		$this->port       = getenv( 'SATELLITE_SMTP_PORT' ) ?: 1025;
		$this->encryption = getenv( 'SATELLITE_SMTP_ENCRYPTION' ) ?: false;
		$this->auth       = getenv( 'SATELLITE_SMTP_AUTH' ) ?: false;
		$this->username   = getenv( 'SATELLITE_SMTP_USERNAME' ) ?: false;
		$this->password   = getenv( 'SATELLITE_SMTP_PASSWORD' ) ?: false;

		add_action( 'phpmailer_init', [ $this, 'phpmailer_init' ], 999 );
		add_action( 'admin_init', [ $this, 'disable_plugins' ], 999 );
	}

	/**
	 * Check if we're on a non-production environment.
	 *
	 * @return bool
	 */
	private function is_safe_environment(): bool {
		return in_array( $this->environment(), [ 'development', 'local' ], true );
	}

	/**
	 * Sets wp_mail() to use PHPMailer as the mailer with SMTP settings.
	 *
	 * @param PHPMailer $phpmailer The PHPMailer instance
	 *
	 * @return void
	 */
	public function phpmailer_init( PHPMailer $phpmailer ) {
		// phpcs:disable WordPress.NamingConventions.ValidVariableName -- these class properties are not our code
		$phpmailer->isSMTP();
		$phpmailer->Host       = $this->host;
		$phpmailer->Port       = $this->port;
		$phpmailer->SMTPSecure = $this->encryption;
		$phpmailer->SMTPAuth   = $this->auth;
		$phpmailer->Username   = $this->username;
		$phpmailer->Password   = $this->password;
		// phpcs:enable
	}

	/**
	 * Checks if any of the plugins in the $plugins array are active.
	 * If so, disable them for selected environments environments.
	 *
	 * @return void
	 */
	public function disable_plugins() {
		if ( ! $this->is_safe_environment() ) {
			return;
		}

		foreach ( $this->plugins as $plugin ) {
			if ( is_plugin_active( $plugin ) ) {
				deactivate_plugins( $plugin, true );
			}
		}
	}
}
