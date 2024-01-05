<?php

namespace MyListing\Ext\Stats;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Stat_Group {

	public $stats = [];

	public function __construct( $stats ) {
		$this->stats = $stats;
	}

	/**
	 * Get a value from the stats array. Nested values can be
	 * accessed using dots, e.g. `$stats->get('visits.browsers')`.
	 *
	 * @since 2.0
	 */
	public function get( $stat ) {
		$parts = explode( '.', $stat );
		$stats = $this->stats;

		foreach ( $parts as $part ) {
			if ( empty( $stats[ $part ] ) ) {
				return false;
			}

			$stats = $stats[ $part ];
		}

		return $stats;
	}

	public function all() {
		return $this->stats;
	}
}