<?php
/**
 * Plugin Name:     Satellite
 * Plugin URI:      https://github.com/eighteen73/satellite
 * Description:     A collection of developer tools for WordPress projects
 * Author:          eighteen73
 * Author URI:      https://eighteen73.co.uk
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

require_once 'autoload.php';

RemoteFiles::instance()->setup();
MailCatcher::instance()->setup();
