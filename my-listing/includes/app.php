<?php

namespace MyListing;

class App {
	use \MyListing\Src\Traits\Instantiatable;

	/**
	 * List of theme options, divided into lazy-loaded groups.
	 *
	 * @since 2.4
	 * @var   array [maps, stats, ...]
	 */
	private $theme_options;

	private $classes;

	public function __construct() {
		$this->theme_options = [];
	}

	public function register( $name, $instance = null ) {
		if ( is_array( $name ) ) {
			foreach ( $name as $classname => $classinstance ) {
				$this->classes[ $classname ] = $classinstance;
			}
			return;
		}

		$this->classes[ $name ] = $instance;
	}

	public function boot( ...$classes ) {
		foreach ( $classes as $classname ) {
			call_user_func( [ $classname, 'boot' ] );
		}
	}

	/**
	 * Retrieve a theme option from an option group. Groups are only fetched
	 * at the time they're requested for better performance.
	 *
	 * Nested options can be safely accessed with dot notation, so in case of
	 * missing nested data, the default value will be returned instead.
	 *
	 * Example use: `mylisting()->get( 'maps.provider', 'google-maps' );`
	 *
	 * @since 2.4
	 */
	public function get( $option, $default = null ) {
		$keys = explode( '.', $option );
		$option_group = $keys[0];

		// if option group isn't present, load it now
		if ( ! isset( $this->theme_options[ $option_group ] ) ) {
			$this->theme_options[ $option_group ] = apply_filters( 'mylisting/load-options:'.$option_group, [] );
		}

		$options = $this->theme_options[ $option_group ];
		unset( $keys[0] );

		// recursively go through the option group to get the option in the specified path
		foreach ( $keys as $key ) {
			if ( ! isset( $options[ $key ] ) ) {
				return $default;
			}

			$options = $options[ $key ];
		}

		return $options;
	}

	public function __call( $method, $args ) {
		if ( isset( $this->classes[ $method ] ) ) {
			return $this->classes[ $method ];
		}

		return null;
	}
}
