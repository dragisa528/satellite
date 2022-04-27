<?php

namespace Eighteen73\Satellite;

/**
 * Abstract class
 */
abstract class Singleton {
	/**
	 * Return instance of class
	 *
	 * @return self
	 */
	public static function instance(): Singleton {
		static $instance;

		if ( empty( $instance ) ) {
			$class = get_called_class();

			$instance = new $class();

			if ( method_exists( $instance, 'setup' ) ) {
				$instance->setup();
			}
		}

		return $instance;
	}
}
