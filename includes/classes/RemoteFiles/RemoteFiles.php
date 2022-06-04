<?php
/**
 * Hotlink to uploaded files from a remote website
 *
 * @package         Satellite
 */

namespace Eighteen73\Satellite\RemoteFiles;

use Eighteen73\Satellite\Environment;
use Eighteen73\Satellite\Singleton;
use Roots\WPConfig\Config;
use Roots\WPConfig\Exceptions\UndefinedConfigKeyException;

/**
 * This class is built upon BE Media from Production so all due credit to those authors.
 * http://www.github.com/billerickson/be-media-from-production
 */
class RemoteFiles extends Singleton {

	use Environment;

	/**
	 * Production URL
	 *
	 * @var string|null
	 */
	public ?string $production_url = null;

	/**
	 * Holds list of upload directories
	 * Can set manually here, or allow function below to automatically create it
	 *
	 * @var array
	 */
	public array $directories = [];

	/**
	 * Primary constructor
	 *
	 * @return void
	 */
	public function setup() {
		// Development only
		if ( ! $this->is_safe_environment() ) {
			return;
		}

		$this->production_url = $this->get_production_url();
		if ( ! $this->production_url ) {
			return;
		}

		// Update Image URLs
		add_filter( 'wp_get_attachment_image_src', [ $this, 'image_src' ] );
		add_filter( 'wp_get_attachment_image_attributes', [ $this, 'image_attr' ], 99 );
		add_filter( 'wp_prepare_attachment_for_js', [ $this, 'image_js' ], 10, 3 );
		add_filter( 'the_content', [ $this, 'image_content' ] );
		add_filter( 'the_content', [ $this, 'image_content_relative' ] );
		add_filter( 'wp_get_attachment_url', [ $this, 'update_image_url' ] );
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
	 * Modify Main Image URL
	 *
	 * @param array $image The image
	 *
	 * @return mixed
	 */
	public function image_src( array $image ) {
		if ( empty( $image ) ) {
			return $image;
		}

		if ( isset( $image[0] ) ) {
			$image[0] = $this->update_image_url( $image[0] );
		}

		return $image;
	}

	/**
	 * Modify Image Attributes
	 *
	 * @param array $attr The image attributes
	 *
	 * @return array
	 */
	public function image_attr( array $attr ): array {

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
	 *
	 * @param array $response The image response
	 *
	 * @return array
	 */
	public function image_js( array $response ): array {

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
	 *
	 * @param string $content HTML content
	 *
	 * @return string
	 */
	public function image_content( string $content ): string {
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
	 *
	 * @param string $content HTML content
	 *
	 * @return string
	 */
	public function image_content_relative( string $content ): string {
		$regex = '/\"\/app\/uploads[^\" ]+/i';
		preg_match_all( $regex, $content, $matches );

		foreach ( $matches[0] as $url ) {
			$url     = str_replace( '"', '', $url );
			$new_url = $this->update_image_url_relative( $url );
			$content = str_replace( $url, $new_url, $content );
		}

		return $content;
	}

	/**
	 * Convert a URL to a local filename
	 *
	 * @param string $url Image URL
	 *
	 * @return string
	 */
	public function local_filename( string $url ): string {
		$upload_locations = wp_upload_dir();

		return str_replace( $upload_locations['baseurl'], $upload_locations['basedir'], $url );
	}

	/**
	 * Determine if local image exists
	 *
	 * @param string $url Image URL
	 *
	 * @return bool
	 */
	public function local_image_exists( string $url ): bool {
		return file_exists( $this->local_filename( $url ) );
	}

	/**
	 * Update Image URL
	 *
	 * @param string $image_url Image URL
	 *
	 * @return string
	 */
	public function update_image_url( string $image_url ): string {

		if ( ! $image_url ) {
			return $image_url;
		}

		if ( $this->local_image_exists( $image_url ) ) {
			return $image_url;
		}

		$production_url = esc_url( $this->production_url );
		if ( empty( $production_url ) ) {
			return $image_url;
		}

		return str_replace( trailingslashit( home_url() ), trailingslashit( $production_url ), $image_url );
	}

	/**
	 * Update Image URL
	 *
	 * @param string $image_url Image URL
	 *
	 * @return string
	 */
	public function update_image_url_relative( string $image_url ): string {

		if ( ! $image_url ) {
			return $image_url;
		}

		if ( $this->local_image_exists( $image_url ) ) {
			return $image_url;
		}

		$production_url = esc_url( $this->production_url );
		if ( empty( $production_url ) ) {
			return $image_url;
		}

		return $production_url . $image_url;
	}


	/**
	 * Return the production URL
	 *
	 * @return string|null
	 */
	public function get_production_url(): ?string {
		try {
			$production_url = getenv( 'SATELLITE_PRODUCTION_URL' ) ?: Config::get( 'SATELLITE_PRODUCTION_URL' );
		} catch ( UndefinedConfigKeyException $e ) {
			return null;
		}

		return apply_filters( 'satellite_url', $production_url );
	}
}
