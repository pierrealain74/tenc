<?php

namespace MyListing\Src;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Promotion_Product extends \WC_Product_Simple {

	public function __construct( $product ) {
		$this->product_type = 'promotion_package';
		parent::__construct( $product );
	}

	public function get_type() {
		return 'promotion_package';
	}

	public function is_virtual() {
		return true;
	}

	public function is_sold_individually() {
		return false;
	}

	/**
	 * Get promotion duration (in days).
	 *
	 * @since 1.7
	 */
	public function get_duration() {
		$duration = $this->get_meta('_promotion_duration');
		return absint( $duration ?: 14 );
	}

	/**
	 * Get the priority level that will be given to listings.
	 *
	 * @since  1.7
	 */
	public function get_priority() {
		$priority = $this->get_meta('_promotion_priority');
		return absint( $priority ?: 2 );
	}
}
