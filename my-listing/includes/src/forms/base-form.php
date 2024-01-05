<?php
/**
 * Form management class, used for Add Listing, Edit Listing, and Claim Listing forms.
 *
 * @since 2.1
 *
 * @license GNU General Public License v3.0 (https://www.gnu.org/licenses/gpl-3.0.html)
 *
 * @copyright:
 *     2019 27collective https://27collective.net/
 *     2018 Automattic   https://automattic.com/
 *
 */

namespace MyListing\Src\Forms;

if ( ! defined('ABSPATH') ) {
	exit;
}

/**
 * Parent abstract class for form classes.
 *
 * @since 2.1
 */
abstract class Base_Form {

	// form fields
	protected $fields = [];

	// form action
	protected $action = '';

	// form errors
	protected $errors = [];

	// form steps
	protected $steps = [];

	// current form step
	protected $step = 0;

	// form name
	public $form_name = '';

	/**
	 * Processes the form result and can also change view if step is complete.
	 *
	 * @since 2.1
	 */
	public function process() {
		$step_key = $this->get_step_key( $this->step );

		if ( $step_key && is_callable( $this->steps[ $step_key ]['handler'] ) ) {
			call_user_func( $this->steps[ $step_key ]['handler'] );
		}

		$next_step_key = $this->get_step_key( $this->step );

		// if the step changed, but the next step has no 'view', call the next handler in sequence.
		if ( $next_step_key && $step_key !== $next_step_key && ! is_callable( $this->steps[ $next_step_key ]['view'] ) ) {
			$this->process();
		}
	}

	/**
	 * Calls the view handler if set, otherwise call the next handler.
	 *
	 * @since 2.1
	 */
	public function output( $atts = [] ) {
		$step_key = $this->get_step_key( $this->step );
		$this->show_errors();

		if ( $step_key && is_callable( $this->steps[ $step_key ]['view'] ) ) {
			call_user_func( $this->steps[ $step_key ]['view'], $atts );
		}
	}

	public function render() {
		$this->output();
	}

	/**
	 * Adds an error.
	 *
	 * @since 2.1
	 */
	public function add_error( $error ) {
		$this->errors[] = $error;
	}

	/**
	 * Displays errors.
	 *
	 * @since 2.1
	 */
	public function show_errors() {
		foreach ( $this->errors as $error ) {
			echo '<div class="job-manager-error">' . wp_kses_post( $error ) . '</div>';
		}
	}

	/**
	 * Gets the action (URL for forms to post to).
	 *
	 * @since 2.1
	 */
	public function get_action() {
		return esc_url_raw( $this->action ? $this->action : wp_unslash( $_SERVER['REQUEST_URI'] ) );
	}

	/**
	 * Gets form name.
	 *
	 * @since 2.1
	 */
	public function get_form_name() {
		return $this->form_name;
	}

	/**
	 * Gets steps from outside of the class.
	 *
	 * @since 2.1
	 */
	public function get_steps() {
		return $this->steps;
	}

	/**
	 * Gets step from outside of the class.
	 *
	 * @since 2.1
	 */
	public function get_step() {
		return $this->step;
	}

	/**
	 * Gets step key from outside of the class.
	 *
	 * @since 2.1
	 */
	public function get_step_key( $step = '' ) {
		if ( ! $step ) {
			$step = $this->step;
		}
		$keys = array_keys( $this->steps );
		return isset( $keys[ $step ] ) ? $keys[ $step ] : '';
	}

	/**
	 * Sets step from outside of the class.
	 *
	 * @since 2.1
	 */
	public function set_step( $step ) {
		$this->step = absint( $step );
	}

	/**
	 * Increases step from outside of the class.
	 *
	 * @since 2.1
	 */
	public function next_step() {
		$this->step++;
	}

	/**
	 * Decreases step from outside of the class.
	 *
	 * @since 2.1
	 */
	public function previous_step() {
		$this->step--;
	}

	/**
	 * Gets fields for form.
	 *
	 * @since 2.1
	 */
	public function get_fields( $key ) {
		if ( empty( $this->fields[ $key ] ) ) {
			return [];
		}

		$fields = $this->fields[ $key ];

		uasort( $fields, [ $this, 'sort_by_priority' ] );

		return $fields;
	}

	/**
	 * Sorts array by priority value.
	 *
	 * @since 2.1
	 */
	protected function sort_by_priority( $a, $b ) {
		if ( floatval( $a['priority'] ) === floatval( $b['priority'] ) ) {
			return 0;
		}
		return ( floatval( $a['priority'] ) < floatval( $b['priority'] ) ) ? -1 : 1;
	}

	/**
	 * Initializes form fields.
	 *
	 * @since 2.1
	 */
	protected function init_fields() {
		$this->fields = [];
	}

	/**
	 * Checks whether reCAPTCHA has been set up and is available.
	 *
	 * @since 2.1
	 */
	public function is_recaptcha_available() {
		$site_key               = mylisting_get_setting( 'recaptcha_site_key' );
		$secret_key             = mylisting_get_setting( 'recaptcha_secret_key' );
		$is_recaptcha_available = ! empty( $site_key ) && ! empty( $secret_key );

		return $is_recaptcha_available;
	}

	/**
	 * Show reCAPTCHA field on the form.
	 *
	 * @since 2.1
	 */
	public function use_recaptcha_field() {
		return false;
	}
}
