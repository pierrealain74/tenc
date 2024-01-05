<?php

namespace MyListing\Controllers;

if ( ! defined('ABSPATH') ) {
	exit;
}

abstract class Base_Controller {

	public static function boot() {
		new static;
	}

	public function __construct() {
		if ( $this->is_active() ) {
			$this->dependencies();
			$this->hooks();
		}
	}

	/**
	 * Add controller hooks (actions, filters, etc.)
	 *
	 * @since 2.5.0
	 */
	abstract protected function hooks();

	/**
	 * Load controller dependencies (classes, files, etc.)
	 *
	 * @since 2.5.0
	 */
	protected function dependencies() {
		//
	}

	/**
	 * Determine whether the controller should be loaded.
	 *
	 * @since 2.6
	 */
	protected function is_active() {
		return true;
	}

	/**
	 * Wrapper for `add_filter` which allows using protected
	 * methods as filter callbacks.
	 *
	 * @since 2.5.0
	 */
	protected function filter( $tag, $function_to_add, $priority = 10, $accepted_args = 1, $run_once = false ) {
		if ( is_string( $function_to_add ) && substr( $function_to_add, 0, 1 ) === '@' ) {
			$method_name = substr( $function_to_add, 1 );
			add_filter( $tag, function() use ( $method_name, $run_once ) {
				static $ran;
				if ( $run_once && $ran === true ) {
					return;
				}

				$ran = true;
				return $this->{$method_name}( ...func_get_args() );
			}, $priority, $accepted_args );
		} else {
			add_filter( $tag, $function_to_add, $priority, $accepted_args );
		}
	}

	/**
	 * Wrapper for `add_action` which allows using protected
	 * methods as action callbacks.
	 *
	 * @since 2.5.0
	 */
	protected function on( $tag, $function_to_add, $priority = 10, $accepted_args = 1 ) {
		return $this->filter( $tag, $function_to_add, $priority, $accepted_args );
	}

	/**
	 * Allows for adding an action hook that only runs once.
	 *
	 * @since 2.5.0
	 */
	protected function once( $tag, $function_to_add, $priority = 10, $accepted_args = 1 ) {
		return $this->filter( $tag, $function_to_add, $priority, $accepted_args, $run_once = true );
	}
}
