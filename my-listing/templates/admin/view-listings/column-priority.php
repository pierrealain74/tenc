<?php

// @todo custom icons for different priority levels.
$priority = $listing->get_priority();
if ( $priority === 0 ) {
	$label = _x( 'Normal', 'WP Admin > All Listings Table', 'my-listing' );
} elseif ( $priority === 1 ) {
	$label = _x( 'Featured', 'WP Admin > All Listings Table', 'my-listing' );
} elseif ( $priority === 2 ) {
	$label = _x( 'Promoted', 'WP Admin > All Listings Table', 'my-listing' );
} else {
	$label = _x( 'Custom', 'WP Admin > All Listings Table', 'my-listing' );
}

printf(
	'<span class="priority priority-%d" title="Priority: %d">%s</span>',
	$priority,
	$priority,
	$label
);