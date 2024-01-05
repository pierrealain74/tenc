<?php

namespace MyListing\Src;

class Schema {

	public $listing;
	private $hash;

	public function __construct( $listing ) {
		$this->listing = $listing;
		$this->hash    = bin2hex( openssl_random_pseudo_bytes( 20 ) );
	}

	public function get_markup() {
		if ( ! ( $this->listing && $this->listing->type )  ) {
			return;
		}

		add_filter( 'mylisting/compile-string-field', [ $this, 'field_value' ], 50, 4 );

		$markup = $this->compile( $this->listing->type->get_schema_markup() );

		remove_filter( 'mylisting/compile-string-field', [ $this, 'field_value' ], 50 );

		// if no reviews/rating is available, don't output aggregateScore at all as it's invalid
		if ( isset( $markup['aggregateRating'] ) ) {
			if ( ! isset( $markup['aggregateRating']['ratingValue'], $markup['aggregateRating']['reviewCount'] ) ) {
				unset( $markup['aggregateRating'] );
			}
		}

		// Generate code.
		$output = '<script type="application/ld+json">' . PHP_EOL;
		$output .= wp_json_encode( $markup, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
		$output .= PHP_EOL . '</script>';

		return $output;
	}

	public function compile( $data = [] ) {
		$markup = [];

		foreach ( $data as $prop => $value ) {
			if ( ! $compiled_prop = $this->listing->compile_string( $prop ) ) {
				continue;
			}

			if ( is_array( $value ) ) {
				$compiled_value = $this->compile( $value );
			} else {
				$compiled_value = $this->listing->compile_string( $value );
			}

			if ( ! $compiled_value ) {
				continue;
			}

			if ( is_string( $compiled_value ) && strpos( $compiled_value, $this->hash ) !== false ) {
				$compiled_value = str_replace( $this->hash, '', $compiled_value );
				$compiled_value = json_decode( base64_decode( $compiled_value ) );
			}

			$markup[ $compiled_prop ] = $compiled_value;
		}

		if ( empty( $markup ) ) {
			return new \stdClass;
		}

		return $markup;
	}

	public function field_value( $value, $field, $modifier, $listing ) {
		if ( $field->get_key() === 'work_hours' ) {
			$value = $listing->schedule->schema_format();
		}

		if ( $field->get_key() === 'links' ) {
			$value = array_filter( array_map( function( $link ) {
				return ! empty( $link['url'] ) ? $link['url'] : false;
			}, (array) $field->get_value() ) );

			$value = array_values( $value );
		}

		if ( in_array( $field->get_key(), ['description', 'job_description'] ) ) {
			$value = wp_kses( $value, [] );
		}

		if ( empty( $value ) ) {
			$value = '';
		}

		/*
		 * $listing->compile_string() excepts the return value to be a string.
		 * Since we need the value to be an array or object to be outputted as json+ld,
		 * we encode it as json + base64 now, and decode it later when outputting data.
		 *
		 * The random hash is attached as a security measure,
		 * so user entered json encoded data doesn't get decoded.
		 */
		if ( is_array( $value ) || is_object( $value ) ) {
			$value = $this->hash . base64_encode( json_encode( $value ) );
		}

		return $value;
	}

}