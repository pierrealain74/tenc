<?php
$data = c27()->merge_options( [
    'rating' => false,
    'max-rating' => false,
    'class' => 'listing-rating',
], $data );

// Validate.
if ( ! ( $data['rating'] >= 1 && $data['max-rating'] >= 1 && $data['rating'] <= $data['max-rating'] ) ) {
	return;
}

// Convert to 5 star rating and round to nearest .5
$rating = (float) round( ( ( $data['rating'] / $data['max-rating'] ) * 5 ) * 2 ) / 2;
$fullstars = (float) floor( $rating );
$half_star = ( $rating - $fullstars ) === 0.5;
$empty_stars = 5 - ( $fullstars + ( $half_star ? 1 : 0 ) );

/* Output markup */
// Open wrapper div.
if ( ! empty( $data['class'] ) ) {
	printf( '<div class="%s">', esc_attr( $data['class'] ) );
}

// Output full stars.
for ( $i = 1; $i <= $fullstars; $i++ ) { echo '<i class="mi star"></i>'; }

// Output half star.
if ( $half_star ) { echo '<i class="mi star_half"></i>'; }

// Output empty stars.
for ( $i = 1; $i <= $empty_stars; $i++ ) { echo '<i class="mi star_border"></i>'; }

// Close wrapper div.
if ( ! empty( $data['class'] ) ) {
	printf( '</div>' );
}
