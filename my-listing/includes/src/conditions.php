<?php

namespace MyListing\Src;

class Conditions {
	private $field, $conditions, $listing, $package_id;

	public function __construct( $field, $listing = null ) {
		$this->field = $field;
		$this->conditions = ! empty( $field->get_prop('conditions') ) ? $field->get_prop('conditions') : [];
		$this->conditional_logic = ! empty( $field->get_prop('conditional_logic') ) ? $field->get_prop('conditional_logic') : false;
		$this->listing = $listing;
		$this->package_id = $this->get_package_id();
	}

	public function passes() {
		$results = [];

		// If there's no conditional logic, show the field.
		if ( ! $this->conditional_logic ) {
			return true;
		}

		// Title needs to always be visible.
		if ( ! empty( $this->field->get_prop('slug') ) && in_array( $this->field->get_prop('slug'), [ 'job_title' ] ) ) {
			return true;
		}

		$this->conditions = array_filter( $this->conditions );

		// Return true if there isn't any condition set.
		if ( empty( $this->conditions ) ) {
			return true;
		}

		// Loop through the condition blocks.
		// First level items consists of arrays related as "OR".
		// Second level items consists of conditions related as "AND".
		// dump( sprintf( 'Looping through %s condition groups...', $this->field->get_prop('slug') ) );
		foreach ( $this->conditions as $conditionGroup ) {
			if ( empty( $conditionGroup ) ) {
				continue;
			}

			foreach ( $conditionGroup as $condition ) {
				if ( $condition['key'] == '__listing_package' ) {
					if ( ! ( $package_id = $this->package_id ) ) {
						// Special key. Useful for conditions that require no package.
						$package_id = '--none--';
					}

					if ( ! self::compare( $condition, $package_id ) ) {
						// dump( 'Condition failed.', $condition );
						$results[] = false;
						continue(2);
					}

					// dump( 'Condition passed.' );
				}
			}

			$results[] = true;
		}

		// Return true if any of the condition groups is true.
		return in_array( true, $results );
	}

	public static function compare( $condition, $value ) {
		if ( $condition['compare'] == '==' ) {
			return $condition['value'] == $value;
		}

		if ( $condition['compare'] == '!=' ) {
			return $condition['value'] != $value;
		}

		return false;
	}

	/**
	 * Get WC Product ID Related to Listing.
	 * This is used to check the visibility fields using product ID.
	 *
	 * @since unknown
	 *
	 * @return int|false
	 */
	public function get_package_id() {

		// edit listing form
		if ( $this->listing && ! in_array( $this->listing->get_status(), [ 'preview', 'pending_payment' ] ) ) {
			return self::get_package_product_id( $this->listing->get_data( '_user_package_id' ) );
		}

		// add listing form
		if ( ! empty( $_REQUEST['listing_package'] ) ) {
			$post = get_post( $_REQUEST['listing_package'] );
			if ( ! $post ) {
				return false;
			}

			// Is WC Product.
			if ( 'product' === $post->post_type ) {
				return $post->ID;
			}

			// Is (User) Payment Package.
			return self::get_package_product_id( $post->ID );
		}

		// Package not found.
		return false;
	}

	/**
	 * Get Product ID From User Package.
	 * Use WPJM WC Paid Listing or Internal/C27 Paid Listing Package.
	 *
	 * @since unknown
	 *
	 * @param int $package_id User Package ID.
	 * @return int|false Product ID or false.
	 */
	public static function get_package_product_id( $user_package_id ) {
		$package = false;

		// Bail early if not set.
		if ( ! $user_package_id || ! is_numeric( $user_package_id ) ) {
			return false;
		}

		$package = \MyListing\Src\Package::get( $user_package_id );
		if ( ! ( $package && ( $product_id = $package->get_product_id() ) ) ) {
			return false;
		}

		return $product_id;
	}
}
