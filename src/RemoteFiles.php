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
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $production_url = '';

	/**
	 * Holds list of upload directories
	 * Can set manually here, or allow function below to automatically create it
	 *
	 * @since 1.0.0
	 * @var array
	 */
	public $directories = array();

	/**
	 * Start Month
	 *
	 * @since 1.0.0
	 * @var int
	 */
	public $start_month = false;

	/**
	 * Start Year
	 *
	 * @since 1.0.0
	 * @var int
	 */
	public $start_year = false;

	/**
	 * Primary constructor.
	 *
	 * @since 1.0.0
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
	 *
	 * @param array $image
	 *
	 * @return array $image
	 * @since 1.0.0
	 */
	function image_src( $image ) {

		if ( isset( $image[0] ) ) {
			$image[0] = $this->update_image_url( $image[0] );
		}

		return $image;

	}

	/**
	 * Modify Image Attributes
	 *
	 * @param array $attr
	 *
	 * @return array $attr
	 * @since 1.0.0
	 */
	function image_attr( $attr ) {

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
	 * @param array $response Array of prepared attachment data
	 * @param int|object $attachment Attachment ID or object
	 * @param array $meta Array of attachment metadata
	 *
	 * @return array     $response   Modified attachment data
	 * @since 1.3.0
	 */
	function image_js( $response, $attachment, $meta ) {

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
	 * @param string $content
	 *
	 * @return string $content
	 * @since 1.2.0
	 */
	function image_content( $content ) {
		$upload_locations = wp_upload_dir();

		$regex = '/https?:\/\/[^\" ]+/i';
		preg_match_all( $regex, $content, $matches );

		foreach ( $matches[0] as $url ) {
			if ( false !== strpos( $url, $upload_locations['baseurl'] ) ) {
				$new_url = $this->update_image_url( $url );
				// echo $url."<staing>";
				$content = str_replace( $url, $new_url, $content );
			}
		}

		return $content;
	}

	/**
	 * Modify Images in Content
	 *
	 * @param string $content
	 *
	 * @return string $content
	 * @since 1.2.0
	 */
	function image_content_relative( $content ) {
		$upload_locations = wp_upload_dir();

		$regex = '/\"\/app\/uploads[^\" ]+/i';
		preg_match_all( $regex, $content, $matches );
		//var_dump($matches);

		foreach ( $matches[0] as $url ) {
			// if( false !== strpos( $url, $upload_locations[ 'baseurl' ] ) ) {
			$url     = str_replace( "\"", "", $url );
			$new_url = $this->update_image_url_relative( $url );
			// echo $new_url;
			$content = str_replace( $url, $new_url, $content );
			// }
		}

		return $content;
	}

	/**
	 * Convert a URL to a local filename
	 *
	 * @param string $url
	 *
	 * @return string $local_filename
	 * @since 1.4.0
	 */
	function local_filename( $url ) {
		$upload_locations = wp_upload_dir();
		$local_filename   = str_replace( $upload_locations['baseurl'], $upload_locations['basedir'], $url );

		return $local_filename;
	}

	/**
	 * Determine if local image exists
	 *
	 * @param string $url
	 *
	 * @return boolean
	 * @since 1.4.0
	 */
	function local_image_exists( $url ) {
		return file_exists( $this->local_filename( $url ) );
	}

	/**
	 * Update Image URL
	 *
	 * @param string $image_url
	 *
	 * @return string $image_url
	 * @since 1.0.0
	 */
	function update_image_url( $image_url ) {

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

		$image_url = str_replace( trailingslashit( home_url() ), trailingslashit( $production_url ), $image_url );

		return $image_url;
	}

	/**
	 * Update Image URL
	 *
	 * @param string $image_url
	 *
	 * @return string $image_url
	 * @since 1.0.0
	 */
	function update_image_url_relative( $image_url ) {

		if ( ! $image_url ) {
			return $image_url;
		}

		if ( $this->local_image_exists( $image_url ) ) {
			// echo 'have imagel';
			return $image_url;
		}

		$production_url = esc_url( $this->get_production_url() );
		if ( empty( $production_url ) ) {
			return $image_url;
		}

		$image_url = $production_url . $image_url;

		return $image_url;
	}


	/**
	 * Return the production URL
	 *
	 * First, this method checks if constant `SATELLITE_PRODUCTION_URL`
	 * exists and non-empty. Than applies a filter `satellite_url`.
	 *
	 * @return string
	 * @since 1.5.0
	 */
	public function get_production_url() {
		$production_url = $this->production_url;
		if ( defined( 'SATELLITE_PRODUCTION_URL' ) && SATELLITE_PRODUCTION_URL ) {
			$production_url = SATELLITE_PRODUCTION_URL;
		}

		return apply_filters( 'satellite_url', $production_url );
	}
}
