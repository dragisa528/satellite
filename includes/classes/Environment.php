<?php
/**
 * An environment reader trait for wide installation support
 *
 * @package         Satellite
 */

namespace Eighteen73\Satellite;

trait Environment {

	/**
	 * Adds a fallback to wp_get_environment_type() so this plugin can be used
	 * on older websites that have Bedrock's WP_ENV instead
	 *
	 * @return array|false|string
	 */
	private function environment() {
		if ( defined( 'WP_ENV' ) ) {
			return getenv( 'WP_ENV' );
		}

		return wp_get_environment_type();
	}
}
