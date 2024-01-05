<?php

namespace MyListing\Src\Listing_Types\Filters;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Open_Now extends Base_Filter {
	public function filter_props() {
		$this->props['type'] = 'open-now';
		$this->props['label'] = 'Open Now';
	}

	public function apply_to_query( $args, $form_data ) {
		global $wpdb;

		if ( empty( $form_data[ $this->get_form_key() ] ) ) {
			return $args;
		}


		$indexes = [
			'Monday' => 0,
			'Tuesday' => 1,
			'Wednesday' => 2,
			'Thursday' => 3,
			'Friday' => 4,
			'Saturday' => 5,
			'Sunday' => 6,
		];

		$utc = c27()->utc();
		$index = $indexes[ $utc->format('l') ];
		$day_start = 1440 * $index;
		$minute_utc = $day_start + ( absint( $utc->format('H') ) * 60 ) + absint( $utc->format('i') );

		$site_tz = new \DateTime( 'now', wp_timezone() );
		$index = $indexes[ $site_tz->format('l') ];
		$day_start = 1440 * $index;
		$minute_site_tz = $day_start + ( absint( $site_tz->format('H') ) * 60 ) + absint( $site_tz->format('i') );

		$start_offset = '(-TIMESTAMPDIFF(MINUTE, UTC_TIMESTAMP(), CONVERT_TZ(UTC_TIMESTAMP(), "UTC", timezone)) + CAST(`start` AS SIGNED))';
		$end_offset = '(-TIMESTAMPDIFF(MINUTE, UTC_TIMESTAMP(), CONVERT_TZ(UTC_TIMESTAMP(), "UTC", timezone)) + CAST(`end` AS SIGNED))';

		$sql = <<<SQL
			SELECT listing_id FROM {$wpdb->prefix}mylisting_workhours
			WHERE ( CONVERT_TZ(UTC_TIMESTAMP(), "UTC", "Europe/Tirane") IS NOT NULL AND (
				( {$start_offset} < 0 AND (
					( {$end_offset} > 0 AND (
						{$minute_utc} BETWEEN {$start_offset} + 10080 AND 10080
						OR {$minute_utc} BETWEEN 0 AND {$end_offset}
					) ) OR (
						{$end_offset} < 0 AND {$minute_utc} BETWEEN {$start_offset} + 10080 AND {$end_offset} + 10080
					)
				) ) OR ( {$end_offset} > 10080 AND (
					( {$start_offset} < 10080 AND (
						{$minute_utc} BETWEEN {$start_offset} AND 10080
						OR {$minute_utc} BETWEEN 0 AND {$end_offset} - 10080
					) ) OR (
						{$start_offset} > 10080 AND {$minute_utc} BETWEEN {$start_offset} - 10080 AND {$end_offset} - 10080
					)
				) ) OR (
					{$minute_utc} BETWEEN {$start_offset} AND {$end_offset}
				)
			) ) OR ( CONVERT_TZ(UTC_TIMESTAMP(), "UTC", "Europe/Tirane") IS NULL AND {$minute_site_tz} BETWEEN `start` AND `end` )
			GROUP BY listing_id
		SQL;

		$rows = $wpdb->get_col( $sql );

		$ids = array_map( 'absint', (array) $rows );
		if ( empty( $ids ) ) {
			$ids = [0];
		}

		if ( ! empty( $args['post__in'] ) ) {
			$ids = array_intersect( $args['post__in'], $ids );
			if ( empty( $ids ) ) {
				$ids = [0];
			}
		}

		$args['post__in'] = $ids;
		return $args;
	}

	public function get_choices() {
		$options = (array) ['open' => esc_html__( 'Open Now', 'my-listing' ) ];

		$list = [];
		foreach ( $options as $value => $label ) {
			$list[] = [
	            'value' => urlencode( $value ),
	            'label' => $label,
	            'selected' => false,
	        ];
	    }

	    return $list;
	}

	public function get_request_value() {
		return isset( $_GET[ $this->get_form_key() ] )
			? sanitize_text_field( stripslashes( $_GET[ $this->get_form_key() ] ) )
			: '';
	}
}
