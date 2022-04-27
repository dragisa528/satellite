<?php

namespace Eighteen73\Satellite;

trait EnvReader {
	/**
	 * Supports various website implementations (notably different versions of Bedrock) where
	 * env() is/isn't namespaced
	 */
	private function env( string $key ) {
		if ( class_exists( '\Env\Env' ) && method_exists( '\Env\Env', 'get' ) ) {
			return \Env\Env::get( $key );
		} elseif ( function_exists( 'env' ) ) {
			return env( $key );
		}

		return null;
	}
}
