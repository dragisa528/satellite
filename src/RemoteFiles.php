<?php

namespace Orphans\Satellite;

/**
 * This class is built upon BE Media from Production so all due credit to those authors.
 * http://www.github.com/billerickson/be-media-from-production
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class RemoteFiles {

	/**
	 * Production URL
	 */
	public string $production_url = '';

	/**
	 * Holds list of upload directories
	 * Can set manually here, or allow function below to automatically create it
	 */
	public array $directories = array();

	/**
	 * Primary constructor.
	 */
	function __construct() {

		// Update Image URLs
		add_filter( 'wp_get_attachment_image_src', array( $this, 'image_src' ) );
		add_filter( 'wp_get_attachment_image_attributes', array( $this, 'image_attr' ), 99 );
		add_filter( 'wp_prepare_attachment_for_js', array( $this, 'image_js' ), 10, 3 );
		add_filter( 'the_content', array( $this, 'image_content' ) );
		add_filter( 'the_content', array( $this, 'image_content_relative' ) );
		add_filter( 'wp_get_attachment_url', array( $this, 'update_image_url' ) );
	}

	/**
	 * Modify Main Image URL
	 */
	function image_src( array $image ): array {
		if ( isset( $image[0] ) ) {
			$image[0] = $this->update_image_url( $image[0] );
		}

		return $image;
	}

	/**
	 * Modify Image Attributes
	 */
	function image_attr( array $attr ): array {

		if ( isset( $attr['srcset'] ) ) {
			$srcset = explode( ' ', $attr['srcset'] );
			foreach ( $srcset as $i => $image_url ) {
				$srcset[ $i ] = $this->update_image_url( $image_url );
			}
			$attr['srcset'] = join( ' ', $srcset );
		}

		return $attr;
	}

	/**
	 * Modify Image for Javascript
	 * Primarily used for media library
	 */
	function image_js( array $response ): array {

		if ( isset( $response['url'] ) ) {
			$response['url'] = $this->update_image_url( $response['url'] );
		}

		foreach ( $response['sizes'] as &$size ) {
			$size['url'] = $this->update_image_url( $size['url'] );
		}

		return $response;
	}

	/**
	 * Modify Images in Content
	 */
	function image_content( string $content ): string {
		$upload_locations = wp_upload_dir();

		$regex = '/https?:\/\/[^\" ]+/i';
		preg_match_all( $regex, $content, $matches );

		foreach ( $matches[0] as $url ) {
			if ( false !== strpos( $url, $upload_locations['baseurl'] ) ) {
				$new_url = $this->update_image_url( $url );
				$content = str_replace( $url, $new_url, $content );
			}
		}

		return $content;
	}

	/**
	 * Modify Images in Content
	 */
	function image_content_relative( string $content ): string {
		$regex = '/\"\/app\/uploads[^\" ]+/i';
		preg_match_all( $regex, $content, $matches );

		foreach ( $matches[0] as $url ) {
			$url     = str_replace( "\"", "", $url );
			$new_url = $this->update_image_url_relative( $url );
			$content = str_replace( $url, $new_url, $content );
		}

		return $content;
	}

	/**
	 * Convert a URL to a local filename
	 */
	function local_filename( string $url ): string {
		$upload_locations = wp_upload_dir();

		return str_replace( $upload_locations['baseurl'], $upload_locations['basedir'], $url );
	}

	/**
	 * Determine if local image exists
	 */
	function local_image_exists( string $url ): bool {
		return file_exists( $this->local_filename( $url ) );
	}

	/**
	 * Update Image URL
	 */
	function update_image_url( string $image_url ): string {

		if ( ! $image_url ) {
			return $image_url;
		}

		if ( $this->local_image_exists( $image_url ) ) {
			return $image_url;
		}

		$production_url = esc_url( $this->get_production_url() );
		if ( empty( $production_url ) ) {
			return $image_url;
		}

		return str_replace( trailingslashit( home_url() ), trailingslashit( $production_url ), $image_url );
	}

	/**
	 * Update Image URL
	 */
	function update_image_url_relative( string $image_url ): string {

		if ( ! $image_url ) {
			return $image_url;
		}

		if ( $this->local_image_exists( $image_url ) ) {
			return $image_url;
		}

		$production_url = esc_url( $this->get_production_url() );
		if ( empty( $production_url ) ) {
			return $image_url;
		}

		return $production_url . $image_url;
	}


	/**
	 * Return the production URL
	 *
	 * First, this method checks if constant `SATELLITE_PRODUCTION_URL`
	 * exists and non-empty. Then applies a filter `satellite_url`.
	 */
	public function get_production_url(): string {
		$production_url = $this->production_url;
		if ( defined( 'SATELLITE_PRODUCTION_URL' ) && SATELLITE_PRODUCTION_URL ) {
			$production_url = SATELLITE_PRODUCTION_URL;
		}

		return apply_filters( 'satellite_url', $production_url );
	}
}
