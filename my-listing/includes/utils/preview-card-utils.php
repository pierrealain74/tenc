<?php

namespace MyListing;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return the preview card markup for the requested listing. If preview cache
 * is enabled and this listing isn't cached, then this will create the cache.
 *
 * @since 2.2.3
 */
function get_preview_card( $listing_id ) {
	$listing_id = absint( $listing_id );
	if ( ! $listing_id ) {
		return;
	}

	$cache_enabled = (bool) get_option( 'mylisting_cache_previews' );

	// if cache is not enabled, load the card as previously
	if ( ! $cache_enabled ) {
		$listing = \MyListing\Src\Listing::get( $listing_id );
		ob_start();
		mylisting_locate_template( 'partials/listing-preview.php', [
			'listing' => $listing ? $listing->get_data() : null,
		] );
		return ob_get_clean();
	}

	// check if preview card cache exists
	$dir = trailingslashit( wp_upload_dir()['basedir'] ).'preview-cards/'.\MyListing\nearest_thousands( $listing_id );
	$filepath = trailingslashit( $dir ).$listing_id.'.html';

	if ( ! file_exists( $filepath ) ) {
		\MyListing\cache_preview_card( $listing_id );
	}

	return apply_filters( 'mylisting/get-preview-card-cache', file_get_contents( $filepath ), $listing_id );
}

/**
 * Create or overwrite the preview card cache for the requested listing.
 *
 * @since 2.2.3
 */
function cache_preview_card( $listing_id ) {
	$listing_id = absint( $listing_id );
	$listing = \MyListing\Src\Listing::get( $listing_id );
	$cache_enabled = (bool) get_option( 'mylisting_cache_previews' );
	if ( ! ( $listing && $cache_enabled ) ) {
		return;
	}

	$dir = trailingslashit( wp_upload_dir()['basedir'] ).'preview-cards/'.\MyListing\nearest_thousands( $listing_id );
	if ( ! is_dir( $dir ) ) {
		wp_mkdir_p( $dir );
	}

	ob_start();
	mylisting_locate_template( 'partials/listing-preview.php', [
		'listing' => $listing->get_data(),
		'is_caching' => true,
	] );
	$content = ob_get_clean();

	$fp = fopen( trailingslashit( $dir ).$listing_id.'.html', 'wb' );
	fwrite( $fp, \MyListing\minify_html( $content ) );
	fclose( $fp );

	return $content;
}

/**
 * Delete preview card cache for the given listing.
 *
 * @since 2.2.3
 */
function delete_cached_preview_card( $listing_id ) {
	$dir = trailingslashit( wp_upload_dir()['basedir'] ).'preview-cards/'.\MyListing\nearest_thousands( $listing_id );
	$filepath = trailingslashit( $dir ).$listing_id.'.html';
	if ( file_exists( $filepath ) ) {
		@unlink( $filepath );
	}
}

/**
 * Some preview card values need special handling to work with caching, such
 * as the work hours status, bookmark status, upcoming event date, etc. The
 * field values are stored in the preview-card cache itself and they're calculated
 * every time the preview card is requested, without making any database calls.
 *
 * @since 2.4.5
 */
function prepare_string_for_cache( $string, $listing ) {
    $attributes = [];
    $classes = [];

	preg_match_all( '/\[\[+(?P<fields>.*?)\]\]/', $string, $matches );

	foreach ( array_unique( $matches['fields'] ) as $slug ) {
		$parts = explode( '.', $slug );
		$field_key = $parts[0];
		$modifier = isset( $parts[1] ) ? $parts[1] : null;
		$field = $listing->get_field_object( $field_key );
		if ( ! $field ) {
			continue;
		}

        if ( $field->get_type() === 'recurring-date' ) {
            $string = str_replace(
                sprintf( '[[%s]]', $slug ),
                sprintf( '<var #upcoming="%s"></var>', esc_attr( $slug ) ),
                $string
            );

            $attributes[] = sprintf( '<var #upcoming-visible="%s"></var>', esc_attr( $field_key ) );

            add_filter( 'mylisting/preview-'.$listing->get_id().'/vars', function( $vars ) use ( $field ) {
                if ( ! isset( $vars['upcoming'] ) ) {
                	$vars['upcoming'] = [];
                }

                $vars['upcoming'][ $field->get_key() ] = $field->get_value();
                return $vars;
            } );
        }

        if ( $field->get_type() === 'work-hours' ) {
        	$string = str_replace( sprintf( '[[%s]]', $field_key ), '<var #open-now></var>', $string );
            $attributes[] = '<var #open-now-visible></var>';
            $classes[] = 'open-status';
            $classes[] = '<var #open-now-status></var>';
			add_filter( 'mylisting/preview-'.$listing->get_id().'/vars', function( $vars ) use( $listing ) {
		        $vars['open-now'] = $listing->schedule->get_short_format();
		        return $vars;
		    } );
        }
	}

	return [ $string, array_unique( $attributes ), array_unique( $classes ) ];
}

/**
 * Replace placeholder values for dynamic cache fields that
 * were added by `\MyListing\prepare_string_for_cache`.
 *
 * @since 2.3
 */
function prepare_cache_for_retrieval( $html, $listing_id ) {
	// get listing data stored in the preview cache footer (to avoid db calls)
	$data = [];
	preg_match( '/<div hidden \#vars>(?<data>.*)<\/div \#vars>$/', $html, $matches );
	if ( ! empty( $matches['data'] ) ) {
		$matched_data = json_decode( $matches['data'], ARRAY_A );
        if ( json_last_error() === JSON_ERROR_NONE && ! empty( $matched_data ) && is_array( $matched_data ) ) {
        	$data = $matched_data;
        }

        // remove this data from the returned html
		$html = preg_replace( '/<div hidden \#vars>(?<vars>.*)<\/div \#vars>$/', '', $html );
	}

	$replacements = [];
	foreach ( $data as $var => $value ) {
		if ( $var === 'open-now' ) {
			$schedule = \MyListing\Src\Work_Hours::parse_short_format( $value );
			$status = $schedule->get_status();
			$visible = $status === 'not-available' ? 'style="display:none;"' : '';

			$replacements['<var #open-now-visible></var>'] = $visible;
			$replacements['<var #open-now-status></var>'] = 'listing-status-'.$status;
			$replacements['<var #open-now></var>'] = $schedule->get_label_for_preview_card();
		}

		if ( $var === 'upcoming' ) {
			foreach ( $value as $field_key => $upcoming ) {
				// private filter; set the reference date for upcoming events, used in Explore
				// page to display results relevant to the search timeframe
				$reference = apply_filters( sprintf(
					'_mylisting/recurring-dates/%s:reference', $field_key
				), 'now' );

				$dates = \MyListing\Src\Recurring_Dates\get_upcoming_instances( $upcoming, 1, $reference );
				if ( empty( $dates ) ) {
					$dates = \MyListing\Src\Recurring_Dates\get_previous_instances( $upcoming, 1, $reference );
				}

				if ( ! empty( $dates ) ) {
					$visible = '';
					$datetime = \MyListing\Src\Recurring_Dates\display_instance( $dates[0], 'datetime', $reference );
					$date = \MyListing\Src\Recurring_Dates\display_instance( $dates[0], 'date', $reference );
					$time = \MyListing\Src\Recurring_Dates\display_instance( $dates[0], 'time', $reference );
					$status = \MyListing\Src\Recurring_Dates\display_instance( $dates[0], 'status', $reference );
					$start = \MyListing\Src\Recurring_Dates\display_instance( $dates[0], 'start', $reference );
					$end = \MyListing\Src\Recurring_Dates\display_instance( $dates[0], 'end', $reference );
				} else {
					$visible = 'style="display:none;"';
					$datetime = $date = $time = $status = $start = $end = '';
				}

				$replacements[ sprintf( '<var #upcoming-visible="%s"></var>', $field_key ) ] = $visible;
				$replacements[ sprintf( '<var #upcoming="%s"></var>', $field_key ) ] = $datetime;
				$replacements[ sprintf( '<var #upcoming="%s.date"></var>', $field_key ) ] = $date;
				$replacements[ sprintf( '<var #upcoming="%s.time"></var>', $field_key ) ] = $time;
				$replacements[ sprintf( '<var #upcoming="%s.status"></var>', $field_key ) ] = $status;
				$replacements[ sprintf( '<var #upcoming="%s.start"></var>', $field_key ) ] = $start;
				$replacements[ sprintf( '<var #upcoming="%s.end"></var>', $field_key ) ] = $end;
			}
		}
	}

	$bookmarked = \MyListing\Src\Bookmarks::exists( $listing_id, get_current_user_id() );
	$replacements['<var #saved></var>'] = $bookmarked ? 'bookmarked' : '';

	// run replacements to the html output
	return str_replace(
		array_keys( $replacements ),
		array_values( $replacements ),
		$html
	);
}
