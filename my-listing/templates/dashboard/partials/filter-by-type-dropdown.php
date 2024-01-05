<?php
/**
 * Display the "Filter by Listing Type" dropdown.
 *
 * @since 1.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>
<div class="col-md-3 sort-my-listings">
	<?php if ( count( $listing_types ) >= 2 ): ?>
		<select class="custom-select filter-listing-type-select" required="required">
			<option value="<?php echo esc_url( $endpoint ) ?>" <?php selected( $active_type === 'all' ) ?>>
				<?php _ex( 'All Listing Types', 'User dashboard', 'my-listing' ) ?>
			</option>

			<?php foreach ( $listing_types as $type ): ?>
				<option
					value="<?php echo esc_url( add_query_arg( 'filter_by_type', $type->get_slug(), $endpoint ) ) ?>"
					<?php selected( $active_type === $type->get_slug() ) ?>
				>
					<?php echo esc_html( $type->get_plural_name() ); ?>
				</option>
			<?php endforeach; ?>
		</select>
	<?php endif ?>
</div>
